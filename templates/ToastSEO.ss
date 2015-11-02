<title><% if $SEOTitle %>$SEOTitle<% else_if $MetaTitle %>$MetaTitle | $SiteConfig.Title<% else %>$Title | $SiteConfig.Title<% end_if %></title>
<% if $MetaDescription %>
    <% if $robotsIndex %>
        <meta name="robots" content="$robotsIndex, $robotsFollow">
    <% else %>
        <meta name="robots" content="index, follow">
    <% end_if %>
    <meta name="description" content="$MetaDescription"/>
    <% if $FocusKeyword %>
        <meta name="keywords" content="$FocusKeyword">
    <% end_if %>
    <% if $MetaAuthor %>
        <meta name="Author" content="$MetaAuthor">
    <% end_if %>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8"/>
<% else %>
    $MetaTags(false)
<% end_if %>