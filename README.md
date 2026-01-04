# Table of Content

A lightweight PHP library for automatically generating a Table of Contents from HTML article content. The library parses your HTML, extracts headings, creates anchor links, and provides structured data for building navigation.

## 🎯 Key Features

- **Automatic heading extraction** - Parses `<h2>` tags and generates URL-friendly anchor IDs
- **Title and perex detection** - Automatically extracts the main title (`<h1>`) and introductory paragraph
- **XSS-safe output** - All generated attributes are properly escaped to prevent security vulnerabilities
- **Immutable response object** - Returns a clean, typed `Response` entity with all extracted data
- **Zero configuration** - Works out of the box with sensible defaults
- **PHP 8.0+ support** - Uses modern PHP features including named arguments and constructor property promotion

## 🏗️ Architecture Overview

The library consists of two main components working together:

```
┌─────────────────────────────────────────────────────────────────┐
│                        HTML Input                               │
│  <h1>Title</h1><p>Perex...</p><h2>Section 1</h2>...            │
└─────────────────────────────────┬───────────────────────────────┘
                                  │
                                  ▼
┌─────────────────────────────────────────────────────────────────┐
│                      ContentManager                             │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │  • Parses <h2> headings                                   │  │
│  │  • Generates webalized anchor IDs (slug format)           │  │
│  │  • Injects <div> anchors before each heading              │  │
│  │  • Extracts <h1> title                                    │  │
│  │  • Extracts first <p> as perex                            │  │
│  └───────────────────────────────────────────────────────────┘  │
└─────────────────────────────────┬───────────────────────────────┘
                                  │
                                  ▼
┌─────────────────────────────────────────────────────────────────┐
│                         Response                                │
│  ┌───────────────────────────────────────────────────────────┐  │
│  │  • original: string    (unchanged input HTML)             │  │
│  │  • content: string     (HTML with injected anchors)       │  │
│  │  • pureContent: string (content without <h1>)             │  │
│  │  • title: ?string      (extracted from <h1>)              │  │
│  │  • perex: ?string      (extracted from first <p>)         │  │
│  │  • items: array        (id => title mapping)              │  │
│  └───────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

## 🔧 Components

### ContentManager

The main service class responsible for parsing HTML content. It provides a single public method:

- **`parse(string $html): Response`** - Accepts raw HTML and returns a structured `Response` object

**Processing steps:**

1. Scans for all `<h2>` tags in the content
2. For each heading, generates a URL-friendly ID using `Nette\Utils\Strings::webalize()`
3. Injects an anchor `<div>` element before each heading for smooth scroll navigation
4. Extracts the page title from the first `<h1>` tag
5. Extracts the perex (lead paragraph) from the first `<p>` tag
6. Returns all data wrapped in an immutable `Response` object

### Response

An immutable data transfer object implementing `Stringable`. When cast to string, it returns the processed content with anchors.

**Available methods:**

| Method | Return Type | Description |
|--------|-------------|-------------|
| `getOriginal()` | `string` | Returns the original unmodified HTML input |
| `getContent()` | `string` | Returns HTML with injected anchor elements |
| `getPureContent()` | `string` | Returns content without the `<h1>` title tag |
| `getTitle()` | `?string` | Returns the extracted title or `null` |
| `getPerex()` | `?string` | Returns the extracted perex or `null` |
| `getItems()` | `array<string, string>` | Returns anchor ID to heading title mapping |

## 📦 Installation

It's best to use [Composer](https://getcomposer.org) for installation, and you can also find the package on
[Packagist](https://packagist.org/packages/baraja-core/table-of-content) and
[GitHub](https://github.com/baraja-core/table-of-content).

To install, simply use the command:

```shell
$ composer require baraja-core/table-of-content
```

You can use the package manually by creating an instance of the internal classes, or register a DIC extension to link the services directly to the Nette Framework.

### Requirements

- PHP 8.0 or higher
- `nette/utils` ^3.0

## 🚀 Basic Usage

### Simple Example

```php
use Baraja\TableOfContent\ContentManager;

$manager = new ContentManager();

$html = '
<h1>PHP Online Course for Beginners</h1>
<p>PHP is a server-side scripting language designed for modern web applications.</p>
<h2>How to Start?</h2>
<p>First, you need to install PHP on your computer...</p>
<h2>Basic Software</h2>
<p>You will need a code editor and a local server...</p>
<h2>License</h2>
<p>This course is released under MIT license.</p>
';

$response = $manager->parse($html);
```

### Accessing Parsed Data

```php
// Get the title extracted from <h1>
$title = $response->getTitle();
// Result: "PHP Online Course for Beginners"

// Get the perex extracted from the first <p>
$perex = $response->getPerex();
// Result: "PHP is a server-side scripting language designed for modern web applications."

// Get all table of content items (ID => Title)
$items = $response->getItems();
// Result:
// [
//     'how-to-start' => 'How to Start?',
//     'basic-software' => 'Basic Software',
//     'licence' => 'License',
// ]

// Get modified content with anchor elements
$content = $response->getContent();

// Get content without the <h1> tag (useful for separate title rendering)
$pureContent = $response->getPureContent();

// Get the original unmodified HTML
$original = $response->getOriginal();
```

### Rendering the Table of Contents

```php
$items = $response->getItems();

echo '<nav class="table-of-contents">';
echo '<h3>Contents:</h3>';
echo '<ol>';
foreach ($items as $id => $title) {
    echo sprintf('<li><a href="#%s">%s</a></li>', $id, htmlspecialchars($title));
}
echo '</ol>';
echo '</nav>';
```

### Using Response as String

The `Response` object implements `Stringable`, so you can use it directly where a string is expected:

```php
$response = $manager->parse($html);

// Both of these are equivalent:
echo $response;
echo $response->getContent();
```

## 📸 Visual Examples

### Response Entity Structure

The following image shows the structure of the `Response` object after parsing:

![Response entity](doc/response-entity.png)

### Rendered Table of Contents

Example of how a rendered table of contents looks in a real application:

![Rendered content](doc/rendered-content.png)

## 💡 How Anchor Generation Works

When the parser encounters an `<h2>` heading like:

```html
<h2>How to Start?</h2>
```

It transforms it to:

```html
<div id="how-to-start" class="content-anchor"></div><h2>How to Start?</h2>
```

The anchor ID is generated using `Nette\Utils\Strings::webalize()` which:

- Converts text to lowercase
- Replaces spaces with hyphens
- Removes diacritics (accents)
- Strips special characters

This ensures clean, URL-friendly anchor IDs that work reliably across all browsers.

## 🔒 Security

The library implements proper XSS protection:

- All generated `id` attributes are escaped using `htmlspecialchars()` with `ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE` flags
- Protection against innerHTML mXSS vulnerability (nette/nette#1496) is included
- Original content is preserved without modification in `getOriginal()`

## ⚙️ Integration with Nette Framework

For Nette Framework users, you can register the service in your configuration:

```neon
services:
    - Baraja\TableOfContent\ContentManager
```

Then inject it into your presenters or services:

```php
public function __construct(
    private ContentManager $contentManager,
) {
}
```

## 🎨 Styling Recommendations

For smooth scroll behavior to anchors, add this CSS:

```css
html {
    scroll-behavior: smooth;
}

.content-anchor {
    scroll-margin-top: 80px; /* Offset for fixed headers */
}
```

## 👤 Author

**Jan Barasek**

- Website: [https://baraja.cz](https://baraja.cz)
- GitHub: [@baraja-core](https://github.com/baraja-core)

## 📄 License

`baraja-core/table-of-content` is licensed under the MIT license. See the [LICENSE](https://github.com/baraja-core/table-of-content/blob/master/LICENSE) file for more details.
