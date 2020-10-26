<?php

namespace eftec\tests;


use eftec\cloudking\CloudKing;

include_once __DIR__.'/AbstractCloudKingTestCase.php';

class CloudKingTest extends AbstractCloudKingTestCase
{
    protected function setUp()
    {
        $this->ck=new CloudKing();

    }

    /**
     * @throws \Exception
     */
    public function test_eDirective()
    {
        self::assertContains(
            '<header><title>CKService1</title>',
            $this->ck->run()
        );

    }
    /**
     * @throws \Exception
     */
    public function test2()
    {
        $this->ck->addfunction('fn1'
            ,[CloudKing::argPrim('n1','string')]
            ,[CloudKing::argPrim(['n2','string'])]
        );


        self::assertContains(
            '<header><title>CKService1</title>',
            $this->ck->run()
        );

    }
}
