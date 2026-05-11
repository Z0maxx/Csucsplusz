# Csúcsplusz Autósiskola WordPress Theme

A WordPress theme for Csúcsplusz Autósiskola built with Tailwind CSS.

## Installation

1. Upload the `csucsplusz-theme` folder to `wp-content/themes/`
2. Go to WordPress Admin > Appearance > Themes
3. Click "Activate" on the Csúcsplusz Autósiskola theme

## Requirements

- PHP 7.4 or higher
- WordPress 6.0 or higher
- Contact Form 7 (for contact forms)
- Flamingo (optional, for form submissions)

## Setup Instructions

### 1. Create Pages

Create the following pages and set their titles/slugs:

- **Kezdőlap** (Homepage) - Set as front page in Settings > Reading
- **Tanfolyami Adatok** - slug: `tanfolyami-adatok`
- **Írásos Tájékoztató** - slug: `irasos-tajekoztato`
- **Szerződés minta** - slug: `szerzodes-minta`
- **GDPR** - slug: `gdpr`
- **Statisztika** - slug: `statisztika`
- **Online Jelentkezés** - slug: `jelentkezes` (for the signup form)
- **Contact/Form** - slug: `contact` (for Contact Form 7)

### 2. Contact Form 7 Setup

1. Install and activate Contact Form 7 plugin
2. Create your form(s)
3. Copy the shortcode (e.g., `[contact-form-7 id="123"]`)
4. Add it to your page content

### 3. Flamingo Setup

1. Install and activate Flamingo plugin
2. Flamingo will automatically capture Contact Form 7 submissions
3. View submissions in Admin > Flamingo

## Tailwind CSS

The theme uses Tailwind CSS utility classes. The compiled CSS is in:
- `assets/css/tw.css`

To customize, modify your tailwind.config.js and rebuild.

## Theme Features

- **Responsive Design** - Mobile-first approach with Tailwind utilities
- **Header Navigation** - Sticky header with mobile hamburger menu
- **Contact Form 7 Support** - Dedicated contact page template
- **WordPress Standard** - Follows WordPress theme standards and best practices
- **Comments Support** - Built-in comments functionality
- **Search Functionality** - Search page template included
- **Archives** - Category, tag, author, and date archives

## Template Hierarchy

- `front-page.php` - Front page (if created)
- `page.php` - Static pages
- `page-contact.php` - Contact page template
- `archive.php` - Archives (category, tag, author, date)
- `search.php` - Search results
- `404.php` - Not found page
- `index.php` - Fallback template

## Customization

### Navigation
Edit the menu items in `header.php` in the `$menu_items` array.

### Logo
Update the logo URL in the `csucsplusz_get_logo_url()` function in `functions.php`.

### Colors
Modify the Tailwind color utilities in your templates, or rebuild `tw.css` with your custom colors.

## Support

For issues or customization needs, contact the theme author.

## License

GNU General Public License v2 or later
https://www.gnu.org/licenses/gpl-2.0.html
