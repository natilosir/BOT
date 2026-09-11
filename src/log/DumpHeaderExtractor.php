<?php

namespace natilosir\bot\log;

use Symfony\Component\VarDumper\Dumper\HtmlDumper;

class DumpHeaderExtractor extends HtmlDumper {
    public function getHeader() {
        return parent::getDumpHeader();
    }
}