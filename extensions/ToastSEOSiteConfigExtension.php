<?php

/**
 * Class ToastSEOSiteConfigExtension
 */
class ToastSEOSiteConfigExtension extends DataExtension {

    private static $db = array(
        'DefaultSEOMetaTitle' => 'Varchar(255)',
        'DefaultSEOMetaTitlePosition' => 'Varchar(255)'
    );

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields) {

        if (!$fields->fieldByName('Root.Settings')) {
            $fields->addFieldToTab('Root', TabSet::create('Settings'));
        }

        /** -----------------------------------------
         * Details
         * ----------------------------------------*/

        $fields->findOrMakeTab('Root.Settings.SEO');
        $fields->addFieldsToTab('Root.Settings.SEO', array(
            HeaderField::create('', 'SEO Settings'),
            Textfield::create('DefaultSEOMetaTitle', 'Default Meta Title Addition')->setRightTitle('This is additional copy that will be automatically added to all of your pages\' meta titles.'),
            OptionsetField::create('DefaultSEOMetaTitlePosition', 'Default Meta Title Position', array(
                'before'=>'Prepend to the Meta Title',
                'after'=>'Append to the Meta Title'
                ),'before')
        ));

    }

}
