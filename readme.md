URI Access
==========

__URI based Member Access Control for Expression Engine__

Expression Engine only provides access control on a per-template basis. This is
often insufficient if you use a common template for many URIs and only want to
control access to some of them.

This extension handles access control by comparing the current URI to a list of
protected URIs and associated members and groups who have access to them.

If the member attempting access is not in the allowed list, they are redirected
to another URI of your choice - normally a login page.

## Requirements

Developed and tested on PHP 5.4 and Expression Engine 2.7.2.

*Please don't expect it to work below these versions.*

## Installation

Clone or download this repo to `system/expressionengine/third_party/uri_access`

Install the extension via the control panel as normal.

## Configuration

Several settings need adding to your `system\expressionengine\config\config.php` file

### Access map (requried)

This is where routes and associated access control IDs are defined. The format
of the array is as follows:

    $config['uri_access_map'] = [
        '/regular-expression-route1/' => [
            'group_ids' => [id,id,id],
            'user_ids'  => [id,id,id]
        ],
        '/regular-expression-route2/' => [
            'group_ids' => [id,id,id],
            'user_ids'  => [id,id,id]
        ]
    ];

A real world example might be:

    $config['uri_access_map'] = [
        '/^member-zone/' => [
            'group_ids' => [1,5]
        ],
        '/^special\/admin/zone/' => [
            'user_ids'  => [1,2,3]
        ]
    ];

The first item dictates that any URI that begins with `member-zone` is only accessible
to Super Admins or Members. The second dictates any URI that begins with
`special/admin/zone` is only accessible to Member IDs 1, 2 or 3.

Any regular expression can be used as a route, but there are no convenience
features - the regex must be valid.

### Access denied URI (requried)

This is the URI where your users will be redirected to if they do not have
permission for the current page.

    $config['uri_access_denied_url'] = '/login';

### Comparison URI (optional)

The extensions will default to using `ee()->uri->uri_string()` to establish the
current URI. This can be overridden by setting

    $config['uri_access_compare_uri'] = YOUR_CUSTOM_URI_STRING;

### Append rediect location (optional)

    $config['uri_access_append_uri'] = true;

Setting the above variable will append a query string to your redirect URL
in the format

    `from=currentURI`

This can then be used by other code to act as necessary.