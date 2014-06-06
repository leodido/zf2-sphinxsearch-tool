<?php
/**
 * zf2-sphinxsearch-tool
 *
 * @link        https://github.com/ripaclub/zf2-sphinxsearch-tool
 * @copyright   Copyright (c) 2014,
 *              Leonardo Di Donato <leodidonato at gmail dot com>
 *              Leonardo Grasso <me at leonardograsso dot com>
 * @license     http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */
namespace SphinxSearch\Tool\Config\Writer;

use Zend\Config\Config;
use Zend\Config\Processor\Token;
use Zend\Config\Writer\AbstractWriter;

/**
 * Class SphinxConf
 */
class SphinxConf extends AbstractWriter
{

    /**
     * @param array $config
     * @return string
     */
    public function processConfig(array $config)
    {
        $temp = new Config($config, true);
        // Substitute variables
        if (isset($config['variables'])) {
            $vars = array_map(
                function ($x) {
                    return '{' . $x . '}';
                },
                array_keys($config['variables'])
            );
            $vals = array_values($config['variables']);
            $tokens = array_combine($vars, $vals);
            $processor = new Token();
            $processor->setTokens($tokens);
            $processor->process($temp);
        }
        // Remove variables node
        unset($temp->variables);
        // Store daemons config
        $string = '';
        if (isset($temp->searchd)) {
            /** @var Config $searchdConf */
            $searchdConf = $temp->searchd;
            $searchdConf = $searchdConf->toArray();
            /** @var array $searchdConf */
            $string .= 'searchd';
            $string .= PHP_EOL;
            $string .= '{';
            $string .= array_map(
                function ($key) use ($searchdConf) {
                    return $key . ' = ' . $searchdConf[$key] . PHP_EOL;
                },
                array_keys($searchdConf)
            );
            $string .= '}';
        }
        echo $string . PHP_EOL;
//        if (isset($temp->indexer)) {
//
//        }

        // Store indexes config
//        foreach ($temp as $key => $val) {
//            var_dump($key);
//            var_dump($val);
//        }
        // TODO: finish
        $string = '';
        return $string;
    }

} 