<?php

namespace eftec\tests;


use eftec\cloudking\CloudKing;
use PHPUnit\Framework\TestCase;



abstract class AbstractCloudKingTestCase extends TestCase {


    protected $ck;
    public function __construct($name = null, array $data = [], $dataName = '') {
        parent::__construct($name, $data, $dataName);
        $this->ck = new CloudKing('');
    }
	
}