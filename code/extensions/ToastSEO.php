<?php

use SilverStripe\Forms\FieldList;
use SilverStripe\View\Requirements;
use SilverStripe\Control\Director;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\ORM\DataExtension;

/**
 * Class ToastSEO
 *
 * @property string            SEOTitle
 * @property string            FocusKeyword
 * @property string            MetaAuthor
 * @property string            RobotsIndex
 * @property string            RobotsFollow
 *
 * @property SiteTree|ToastSEO owner
 */
class ToastSEO extends DataExtension
{
    private static $db
      = [
        'SEOTitle'     => 'Varchar(512)',
        'FocusKeyword' => 'Varchar(512)',
        'MetaAuthor'   => 'Varchar(512)',
        'RobotsIndex'  => 'Enum("index,noindex","index")',
        'RobotsFollow' => 'Enum("follow,nofollow","follow")'
      ];

    public static $defaults
      = [
        'RobotsIndex'  => 'index',
        'RobotsFollow' => 'follow'
      ];

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        Requirements::javascript(TOAST_SEO_DIR . '/javascript/toast-seo.js');

        $fields->removeByName('Metadata');

        $fields->addFieldToTab('Root.Main',
          ToggleCompositeField::create('Toast SEO', 'Toast SEO', [
            LiteralField::create('',
              '<h2>&nbsp;&nbsp;&nbsp;Toast SEO<img style="position:relative;top:8px;" src="'
              . Director::absoluteBaseURL()
              . 'toast-seo/images/seo.png"></h2>'),
            LiteralField::create('',
              '<div class="toastSeo" style="margin-left:12px;">'),
            LiteralField::create('',
              '<br><strong>Focus Keyword Usage</strong>'),
            LiteralField::create('', '<br>Your focus keyword was found in:'),
            LiteralField::create('', '<br><ul>'),
            LiteralField::create('',
              '<li>Page Title:<strong class="toastSEOTitle"></strong></li>'),

            LiteralField::create('',
              '<li>Page URL: <strong class="toastURLMatch"></strong></li>'),
            LiteralField::create('',
              '<li>First Paragraph:<strong class="toastSEOSummary"></strong></li>'),
            LiteralField::create('',
              '<li>Meta Description:<strong class="toastSEOMeta"></strong></li>'),
            LiteralField::create('', '</ul>'),
            LiteralField::create('',
              '<div class="toastSEOSnippet" style="padding:0 20px 10px;background:white;margin:20px  20px 20px 0;display:block;border: 1px solid grey;"></div>'),
            LiteralField::create('', '</div>'),
            TextField::create('FocusKeyword', 'Page Subject')
              ->addExtraClass('focusWords')
              ->setRightTitle('Pick the main keywords or keyphrase that this page is about.'),
            TextField::create('SEOTitle', 'Meta Title')
              ->setRightTitle('This meta title is generated automatically from the page name. Editing this will change how the page title shows up in google search. Each page title must be unique.'),
            LiteralField::create('',
              '<br><p class="toastSEOMetaCount" style="margin-left: 12px;">The meta description should be limited to 156 characters, <span class="toastSeoChars">6</span> chars left.</p>'),
            TextareaField::create('MetaDescription', 'Meta Description')
              ->addExtraClass('toastSEOMetaText')
              ->setRightTitle('The meta description is often shown as the black text under the title in a search result. For this to work it has to contain the keyword that was searched for.'),
            LiteralField::create('',
              '<div class="toastSEOSummaryText" style="opacity:0;position:relative;height:0;overflow:hidden;">::  '
              . $this->owner->dbObject('Content')->Summary(25) . '</div>'),
            TextField::create('MetaAuthor', 'Author')
              ->setRightTitle('Example: John Doe, j.doe@example.com'),
            LiteralField::create('',
              '<h2 style="padding-left: 10px;">Robots</h2>'),
            OptionsetField::create('RobotsIndex', 'Index', [
              'index'   => 'INDEX',
              'noindex' => 'NOINDEX'
            ]),
            OptionsetField::create('RobotsFollow', 'Follow', [
              'follow'   => 'FOLLOW',
              'nofollow' => 'NOFOLLOW'
            ])
          ]));
    }

    /**
     * @param string $tags
     */
    public function MetaTags(&$tags)
    {
        // Indexing
        if ($this->owner->RobotsIndex && $this->owner->RobotsFollow) {
            $tags .= sprintf('<meta name="robots" content="%s, %s">',
                $this->owner->RobotsIndex, $this->owner->RobotsFollow) . "\n";
        } else {
            $tags .= '<meta name="robots" content="index, follow">' . "\n";
        }

        // Keywords
        if ($this->owner->FocusKeyword) {
            $tags .= sprintf('<meta name="keywords" content="%s">',
                $this->owner->FocusKeyword) . "\n";
        }

        // Author
        if ($this->owner->MetaAuthor) {
            $tags .= '<meta name="Author" content="' . $this->owner->MetaAuthor
              . '">' . "\n";
        }
    }

    public function getToastSEOTitle()
    {
        return $this->owner->SEOTitle
          ?: $this->owner->MetaTitle ?: $this->owner->Title;
    }

    public function getFullToastSEOTitle()
    {
        /** =========================================
         * @var SiteConfig|ToastSEOSiteConfigExtension $siteConfig
        ===========================================*/

        $siteConfig = SiteConfig::current_site_config();

        $base = $this->getToastSEOTitle();

        $addition = $siteConfig->DefaultSEOMetaTitle;

        if ($siteConfig->DefaultSEOMetaTitlePosition == 'before') {
            $title = sprintf('%s %s', $addition, $base);
        } else {
            $title = sprintf('%s %s', $base, $addition);
        }

        $this->owner->extend('updateToastSEOTitle', $title);

        return $title;
    }
}
