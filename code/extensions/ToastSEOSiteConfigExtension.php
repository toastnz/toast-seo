<?php

use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\HeaderField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\OptionsetField;

/**
 * Class ToastSEOSiteConfigExtension
 *
 * @property string     DefaultSEOMetaTitle
 * @property string     DefaultSEOMetaTitlePosition
 *
 * @property SiteConfig owner
 */
class ToastSEOSiteConfigExtension extends DataExtension
{
    private static $db = [
        'DefaultSEOMetaTitle'         => 'Varchar(255)',
        'DefaultSEOMetaTitlePosition' => 'Enum("before,after","before")',
        'IncludeTwitterCardSEO'       => 'Boolean',
        'IncludeOGSEO'                => 'Boolean',
        'TwitterIDSEO'                => 'Varchar(50)',
        'FacebookIDSEO'               => 'Varchar(50)'
    ];

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        if (!$fields->fieldByName('Root.Metadata')) {
            $fields->addFieldToTab('Root', TabSet::create('Metadata'));
        }

        /** -----------------------------------------
         * Details
         * ----------------------------------------*/

        $fields->findOrMakeTab('Root.Metadata.SEO', 'SEO');

        $fields->addFieldsToTab('Root.Metadata.SEO', [
            HeaderField::create('', 'SEO Settings'),
            TextField::create('DefaultSEOMetaTitle', 'Default Meta Title Addition')
                ->setRightTitle('This is additional copy that will be automatically added to all of your pages\' meta titles.'),
            OptionsetField::create('DefaultSEOMetaTitlePosition', 'Default Meta Title Position', [
                'before' => 'Prepend to the Meta Title',
                'after'  => 'Append to the Meta Title'
            ]),
            CheckboxField::create('IncludeTwitterCardSEO', 'Include Twitter Card SEO in Meta Tags'),
            TextField::create('TwitterIDSEO', 'Twitter ID (to include in Twitter Card SEO Meta Tag)'),
            CheckboxField::create('IncludeOGSEO', 'Include OpenGraph in Meta Tags'),
            TextField::create('FacebookIDSEO', 'Facebook ID (to include in OpenGraph SEO Meta Tag)')
        ]);

    }

}
