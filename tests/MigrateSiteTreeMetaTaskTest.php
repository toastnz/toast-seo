<?php

/**
 * Class MigrateSEOTaskTest
 *
 * @mixin PHPUnit_Framework_Assert
 */
class MigrateSEOTaskTest extends SapphireTest
{
    protected static $fixture_file = 'fixtures/MigrateSEOTaskTest.yml';

    protected $extraDataObjects = array('Page');

    public function setUp()
    {
        parent::setUp();

        // Apply the extension to the Page
        DataExtension::add_to_class('Page', 'ToastSEO');
    }

    public function testRunWithOverwrite()
    {
        $oldHomepage = $this->objFromFixture('Page', 'oldHome');

        $this->assertEquals('Home', $oldHomepage->SEOTitle);
    }
}
