<?php

namespace Kibo\Phast;

/**
 * @runTestsInSeparateProcesses
 */
class PhastDocumentFiltersTest extends \PHPUnit_Framework_TestCase {
    public function setUp() {
        $_SERVER += [
            'HTTP_HOST' => 'example.com',
            'REQUEST_URI' => '/',
        ];
    }

    public function testDeploy() {
        $handlersBefore = ob_list_handlers();

        PhastDocumentFilters::deploy([]);

        $handlersAfter = ob_list_handlers();

        $this->assertNotEquals($handlersBefore, $handlersAfter);
    }

    /** @dataProvider shouldApplyData */
    public function testShouldApply($buffer, $documentsOnly = true) {
        $config = ['optimizeHTMLDocumentsOnly' => $documentsOnly];
        $out = PhastDocumentFilters::apply($buffer, $config);
        $this->assertFiltersApplied($out);
    }

    public function shouldApplyData() {
        yield ['<!doctype html><html><head><title>Hello, World!</title></head><body></body></html>'];
        yield ["<!DOCTYPE html>\n<html>\n<body></body>\n</html>"];
        yield ["<?xml version=\"1.0\"?\><!DOCTYPE html>\n<html>\n<body></body>\n</html>"];
        yield ["<!doctype html>\n<html>\n<body></body>\n</html>"];
        yield ["<html>\n<body></body>\n</html>"];
        yield ["    \n<!doctype       html>\n<html>\n<body></body>\n</html>"];
        yield ["<!doctype html>\n<!-- hello -->\n<html>\n<body></body>\n</html>"];
        yield ["<!-- hello -->\n<!doctype html>\n<html>\n<body></body>\n</html>"];
        yield ['<b>Yup</b>', false];
    }

    /**
     * @dataProvider shouldNotApplyData
     */
    public function testShouldNotApply($buffer, $documentsOnly = true) {
        $config = ['optimizeHTMLDocumentsOnly' => $documentsOnly];
        $out = PhastDocumentFilters::apply($buffer, $config);
        $this->assertFiltersNotApplied($out);
    }

    public function shouldNotApplyData() {
        yield ["<html>\n<body>"];
        yield ['<?xml version="1.0"?\><tag>asd</tag>'];
        yield ["\0<html><body></body></html>"];
        yield ['<!doctype html><html amp><body></body></html>'];
        yield ['<!doctype html><html ⚡><body></body></html>'];
        yield ['Not html', false];
    }

    private function assertFiltersApplied($out) {
        $this->assertContains('[Phast]', $out);
    }

    private function assertFiltersNotApplied($out) {
        $this->assertNotContains('[Phast]', $out);
    }
}
