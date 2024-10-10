# Minimal Offcanvas with Cross-Selling Plugin for Shopware 6

This plugin enhances the Shopware 6 off-canvas cart by introducing a minimal view when adding items to the cart and displaying cross-selling products to boost sales.

## Features

- Minimal off-canvas view when adding items to the cart
- Display of cross-selling products in the minimal off-canvas
- Customizable cross-selling group selection per product
- Global configuration for cross-selling group selection

## Requirements

- Shopware 6.4 or higher

## Installation

1. Create a new directory named `MinimalOffcanvasPlugin` in your Shopware 6 `custom/plugins` directory.
2. Copy all the plugin files into this new directory.
3. Install the plugin via the Shopware CLI:

   ```
   bin/console plugin:install --activate MinimalOffcanvasPlugin
   ```

4. Clear the cache:

   ```
   bin/console cache:clear
   ```

5. Rebuild the Storefront:

   ```
   ./psh.phar storefront:build
   ```

## Configuration

### Global Configuration

1. Go to the Shopware 6 Admin Panel
2. Navigate to Settings > System > Plugins
3. Find "Minimal Offcanvas with Cross-Selling" in the plugin list
4. Click on the three dots (...) and select "Config"
5. Set the "Global Cross-Selling Group Index" to your desired default index

### Per-Product Configuration

1. Go to the Shopware 6 Admin Panel
2. Navigate to Catalogue > Products
3. Select a product to edit
4. Scroll down to the "Minimal Offcanvas Cross-Selling" section
5. Set the "Cross-Selling Group Index" for this specific product

## Usage

Once installed and configured, the plugin will automatically:

1. Display a minimal off-canvas view when a customer adds an item to their cart
2. Show cross-selling products in the minimal off-canvas based on the configuration

The cross-selling products are determined in the following order:

1. Product-specific custom field (if set)
2. Global plugin configuration (if set)
3. First available cross-selling group for the product

## Customization

The plugin uses the following main components:

1. `OffCanvasCartPageLoaderDecorator`: Handles the logic for loading cross-selling products.
2. `CartControllerDecorator`: Modifies the add-to-cart behavior to use the minimal template.
3. `offcanvas-cart-minimal.html.twig`: The template for the minimal off-canvas view.

To customize the appearance of the minimal off-canvas or cross-selling products, you can override the following template:

- `Resources/views/storefront/component/checkout/offcanvas-cart-minimal.html.twig`

## Troubleshooting

If you encounter any issues:

1. Make sure you've cleared the cache and rebuilt the Storefront after installation
2. Check the Shopware logs for any error messages
3. Ensure that your products have cross-selling groups configured
4. Verify that the custom fields for cross-selling index are correctly set up

## Support

For support, please open an issue on the plugin's repository or contact the plugin developer.

## Contributing

Contributions are welcome! Please fork the repository and submit a pull request with your changes.

## License

This plugin is released under the MIT License. See the LICENSE file for details.
