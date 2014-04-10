<?php
/**
 * zf-sphinxsearch-tools
 *
 * @link        https://github.com/ripaclub/zf2-sphinxsearch-tools
 * @copyright   Copyright (c) 2014,
 *              Leonardo Di Donato <leodidonato at gmail dot com>
 *              Leonardo Grasso <me at leonardograsso dot com>
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace SphinxSearch\Tools\Writer\Source;

use SphinxSearch\Tools\Writer\Exception\NotValidDocumentException;
use SphinxSearch\Tools\Writer\Source\FieldsAwareTrait;
use SphinxSearch\Tools\Writer\Source\AttributesAwareTrait;

/**
 * Class XML2
 *
 * Efficiently generate XML for Sphinx's xmlpipe2 driver
 */
class XML2 extends \XMLWriter implements SourceInterface
{
    use FieldsAwareTrait;
    use AttributesAwareTrait;

    /**
     * @param array $options
     */
    public function __construct($options = [])
    {
        $defaults = ['indent' => false];
        $options = array_merge($defaults, $options);
        // Store the xml tree in memory
        $this->openMemory();
        if ($options['indent']) {
            $this->setIndent(true);
        }
    }

    /**
     * @param array $doc
     * @throws \SphinxSearch\Tools\Writer\Exception\NotValidDocumentException
     */
    public function addDocument(array $doc)
    {
        $this->startElement('sphinx:document');
        if (!isset($doc['id'])) {
            throw new NotValidDocumentException('Document array must have an element with "id" key');
        }
        $this->writeAttribute('id', $doc['id']);
        foreach ($doc as $key => $value) {
            // Skip the id key since that is an element attribute
            if ($key == 'id') {
                continue;
            }
            $this->startElement($key);
            $this->text($value);
            $this->endElement();
        }
        $this->endElement();
        print $this->outputMemory();
    }

    public function beginOutput()
    {
        $this->startDocument('1.0', 'UTF-8');
        $this->startElement('sphinx:docset');
        $this->startElement('sphinx:schema');
        // Add fields to the schema
        foreach ($this->fields as $field) {
            $this->startElement('sphinx:field');
            $this->writeAttribute('name', $field);
            $this->endElement();
        }
        // Add attributes to the schema
        foreach ($this->attributes as $attributes) {
            $this->startElement('sphinx:attr');
            foreach ($attributes as $key => $value) {
                $this->writeAttribute($key, $value);
            }
            $this->endElement();
        }
        // End sphinx:schema
        $this->endElement();
        print $this->outputMemory();
    }

    public function endOutput()
    {
        // End sphinx:docset
        $this->endElement();
        print $this->outputMemory();
    }
}
