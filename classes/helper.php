<?php

namespace DustPress;

use Twig\Environment;
use Twig\TokenParser\AbstractTokenParser;
use Twig\Token;
use Twig\Node\Node;
use Twig\Compiler;

abstract class Helper {
    protected $params;
    protected $content;

    /**
     * Process the block content and parameters.
     *
     * @param string $content The block content.
     * @param array  $params  The parameters passed to the helper.
     * @return string The processed output.
     */
    abstract public function init( string $content, array $params ): string;

    /**
     * Registers the helper with Twig.
     *
     * @param Environment $twig The Twig environment.
     * @param string      $name The name of the helper.
     */
    public static function register( Environment $twig, string $name, string $helperClass ) {
        $twig->addTokenParser( new class( $name, $helperClass ) extends AbstractTokenParser {
            private $name;
            private $helperClass;

            public function __construct( string $name, string $helperClass )
            {
                $this->name = $name;
                $this->helperClass = $helperClass;
            }

            public function parse( Token $token )
            {
                $stream = $this->parser->getStream();

                // Parse parameters
                $params = [];
                while (!$stream->test( Token::BLOCK_END_TYPE ) ) {
                    $key = $stream->expect( Token::NAME_TYPE )->getValue();
                    $stream->expect( Token::OPERATOR_TYPE, '=' );
                    $value = $stream->expect( Token::STRING_TYPE )->getValue();
                    $params[$key] = $value;
                }
                $stream->expect( Token::BLOCK_END_TYPE );

                // Parse block content
                $body = $this->parser->subparse( [ $this, 'decideBlockEnd' ], true );
                $stream->expect( Token::BLOCK_END_TYPE );

                return new class( $body, $params, $this->helperClass ) extends Node {
                    private $helperClass;
                    private $params;

                    public function __construct( Node $body, array $params, string $helperClass )
                    {
                        $this->helperClass = $helperClass;
                        $this->params = $params;
                        parent::__construct( [ 'body' => $body ], [], $body->getTemplateLine() );
                    }

                    public function compile( Compiler $compiler )
                    {
                        $compiler
                            ->write( "\$helper = new \\{$this->helperClass}();\n" )
                            ->write( "\$params = " . var_export( $this->params, true ) . ";\n" )
                            ->write( "ob_start();\n" )
                            ->subcompile( $this->getNode( 'body' ) )
                            ->write( "\$content = ob_get_clean();\n" )
                            ->write( "echo \$helper->init(\$content, \$params);\n") ;
                    }
                };
            }

            public function decideBlockEnd( Token $token )
            {
                return $token->test( 'end' . $this->name );
            }

            public function getTag()
            {
                return $this->name;
            }
        });
    }
}
