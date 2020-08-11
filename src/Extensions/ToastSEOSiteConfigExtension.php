<?php

namespace ToastNZ\SEO\Extensions;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\ORM\DataExtension;

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
        'DefaultSEOMetaTitlePosition' => 'Enum("before,after","before")'
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

        $fields->addFieldsToTab(
            'Root.Metadata.SEO',
            [
                HeaderField::create('', 'SEO Settings'),
                TextField::create('DefaultSEOMetaTitle', 'Default Meta Title Addition')
                    ->setRightTitle(
                        'This is additional copy that will be automatically added to all of your pages\' meta titles.'
                    ),
                OptionsetField::create(
                    'DefaultSEOMetaTitlePosition',
                    'Default Meta Title Position',
                    [
                        'before' => 'Prepend to the Meta Title',
                        'after'  => 'Append to the Meta Title'
                    ]
                )
            ]
        );
    }
}
