<?php

/**
 * Class te represent a schema loaded from a XML file
 *
 * @package    framewub/block-builder
 * @author     Wubbo Bos <wubbo@wubbobos.nl>
 * @copyright  Copyright (c) Wubbo Bos
 * @license    GPL
 * @link       https://github.com/wubmeister/framewub
 */

namespace Framewub\BlockBuilder\Schema;

use DOMDocument;
use DOMNode;

class Xml extends AbstractSchema
{
    const NS_BLOCK = 'http://framewub.wubbobos.nl/block-builder/block';
    const NS_ELEMENT = 'http://framewub.wubbobos.nl/block-builder/element';

    /**
     * Loads the schema from a file
     *
     * @param string $filename
     *   The path to the file
     */
    public function __construct(string $filename)
    {
        $dom = new DOMDocument();
        $dom->load($filename);

        $this->schema = [];
        $this->parseSchema($dom->documentElement, $this->schema);
    }

    /**
     * Parses a DOM node into schema information
     *
     * @param DOMNode $node
     *   The node to parse
     * @param array $schemaPart
     *   The schema part, passed by reference. This method WILL update the
     *   passed array.
     */
    protected function parseSchema(DOMNode $node, array &$schemaPart)
    {
        if ($node->nodeName == 'schema') {
            for ($child = $node->firstChild; $child; $child = $child->nextSibling) {
                if ($node->nodeType == XML_ELEMENT_NODE) {
                    $this->parseSchema($child, $schemaPart);
                }
            }
        } else {
            if ($node->namespaceURI == self::NS_BLOCK || $node->namespaceURI == self::NS_ELEMENT) {
                $nameKey = $node->namespaceURI == self::NS_BLOCK ? 'block' : 'element';
                $entity = [ $nameKey => $node->localName, 'mods' => [], 'content' => null ];

                foreach ($node->attributes as $key => $attr) {
                    $entity['mods'][$key] = $attr->value;
                }

                for ($child = $node->firstChild; $child; $child = $child->nextSibling) {
                    if ($node->nodeType == XML_ELEMENT_NODE) {
                        if (!is_array($entity['content'])) {
                            $entity['content'] = $entity['content'] ? [ [ 'text' => $entity['content'] ] ] : [];
                        }
                        $this->parseSchema($child, $entity['content']);
                    }
                }

                $schemaPart[] = $entity;
            }
        }
    }
}
