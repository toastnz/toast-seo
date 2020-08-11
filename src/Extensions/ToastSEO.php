<?php

namespace ToastNZ\SEO\Extensions;

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
use SilverStripe\Core\Manifest\ModuleResourceLoader;

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
    private static $db = [
        'SEOTitle'     => 'Varchar(512)',
        'FocusKeyword' => 'Varchar(512)',
        'MetaAuthor'   => 'Varchar(512)',
        'RobotsIndex'  => 'Enum("index,noindex","index")',
        'RobotsFollow' => 'Enum("follow,nofollow","follow")'
    ];

    public static $defaults = [
        'RobotsIndex'  => 'index',
        'RobotsFollow' => 'follow'
    ];

    /**
     * @param FieldList $fields
     */
    public function updateCMSFields(FieldList $fields)
    {
        Requirements::javascript('toastnz/toast-seo:client/javascript/toast-seo.js');
        Requirements::css('toastnz/toast-seo:client/css/toast-seo.css');

        $imageUrl = ModuleResourceLoader::singleton()->resolveUrl('toastnz/toast-seo:client/images/seo.png');

        $seoTitleMessage = 'This meta title is generated automatically from the page name. Editing this will ';
        $seoTitleMessage .= 'changehow the page title shows up in google search. Each page title must be unique.';

        $metaDescriptionMessage = 'The meta description is often shown as the black text under the title ';
        $metaDescriptionMessage .= 'in a search result. For this to work it has to contain the keyword that ';
        $metaDescriptionMessage .= 'was searched for.';

        $topHTML = <<<HTML
<h2 class="toastSeoHeading">&nbsp;&nbsp;&nbsp;Toast SEO <img src="{$imageUrl}" alt="seo"></h2>
<div class="toastSeo">
    <br><strong>Focus Keyword Usage</strong>
    <br>Your focus keyword was found in:
    <br>
    <ul>
        <li>Page Title:<strong class="toastSEOTitle"></strong></li>
        <li>Page URL: <strong class="toastURLMatch"></strong></li>
        <li>First Paragraph:<strong class="toastSEOSummary"></strong></li>
        <li>Meta Description:<strong class="toastSEOMeta"></strong></li>
    </ul>
    <div class="toastSEOSnippet"></div>
</div>
HTML;

        $metaCountHtml = <<<HTML
<br>
<p class="toastSEOMetaCount">
    The meta description should be limited to 156 characters, <span class="toastSeoChars">6</span> chars left.
</p>'
HTML;

        $fields->removeByName('Metadata');

        $fields->addFieldToTab(
            'Root.Main',
            ToggleCompositeField::create(
                'Toast SEO',
                'Toast SEO',
                [
                    LiteralField::create('', $topHtml),
                    TextField::create('FocusKeyword', 'Page Subject')
                        ->addExtraClass('focusWords')
                        ->setRightTitle('Pick the main keywords or keyphrase that this page is about.'),
                    TextField::create('SEOTitle', 'Meta Title')
                        ->setRightTitle($seoTitleMessage),
                    LiteralField::create('', $metaCountHtml),
                    TextareaField::create('MetaDescription', 'Meta Description')
                        ->addExtraClass('toastSEOMetaText')
                        ->setRightTitle($metaDescriptionMessage),
                    LiteralField::create(
                        '',
                        sprintf(
                            '<div class="toastSEOSummaryText">:: %s</div>',
                            $this->owner->dbObject('Content')->Summary(25)
                        )
                    ),
                    TextField::create('MetaAuthor', 'Author')
                        ->setRightTitle('Example: John Doe, j.doe@example.com'),
                    LiteralField::create('', '<h2 style="padding-left: 10px;">Robots</h2>'),
                    OptionsetField::create(
                        'RobotsIndex',
                        'Index',
                        [
                            'index'   => 'INDEX',
                            'noindex' => 'NOINDEX'
                        ]
                    ),
                    OptionsetField::create(
                        'RobotsFollow',
                        'Follow',
                        [
                            'follow'   => 'FOLLOW',
                            'nofollow' => 'NOFOLLOW'
                        ]
                    ),
                ]
            )
        );
    }

    /**
     * @param string $tags
     */
    public function MetaTags(&$tags)
    {
        // Indexing
        if ($this->owner->RobotsIndex && $this->owner->RobotsFollow) {
            $tags .= sprintf(
                '<meta name="robots" content="%s, %s">',
                $this->owner->RobotsIndex, $this->owner->RobotsFollow
            ) . "\n";
        } else {
            $tags .= '<meta name="robots" content="index, follow">' . "\n";
        }

        // Keywords
        if ($this->owner->FocusKeyword) {
            $tags .= sprintf('<meta name="keywords" content="%s">', $this->owner->FocusKeyword) . "\n";
        }

        // Author
        if ($this->owner->MetaAuthor) {
            $tags .= '<meta name="Author" content="' . $this->owner->MetaAuthor . '">' . "\n";
        }
    }

    public function getToastSEOTitle()
    {
        return $this->owner->SEOTitle ?: $this->owner->MetaTitle ?: $this->owner->Title;
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
