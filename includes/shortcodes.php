<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function royalty_calculator_shortcode($atts = []) {
    $atts = shortcode_atts(
        array(
            'type' => 'general',
        ),
        $atts,
        'royalty_calculator'
    );

    $calculator_type = $atts['type'];

    if ($calculator_type === 'kdp') {
        $output = kdp_royalty_calculator_html();
    } else {
        $output = general_royalty_calculator_html();
    }

    return $output;
}
add_shortcode('royalty_calculator', 'royalty_calculator_shortcode');

function general_royalty_calculator_html() {
    ob_start();
    ?>
    <div class="royalty-calculator" id="general-calculator">
        <h2>Book Royalties Calculator</h2>
        <form id="royaltyForm">
            <label for="royaltyPercentage">Royalty Percentage:</label>
            <input type="number" id="royaltyPercentage" name="royaltyPercentage" value="14" min="0" max="100" step="0.1" required>%

            <label for="bookPrice">Book Price:</label>
            $<input type="number" id="bookPrice" name="bookPrice" value="18" min="0" step="0.01" required>

            <label for="hasAgent">
                <input type="checkbox" id="hasAgent" name="hasAgent" checked>
                Has Agent (15% commission)
            </label>

            <label for="salesQuantity">Number of Books Sold:</label>
            <input type="number" id="salesQuantity" name="salesQuantity" value="1000" min="1" step="1" required>

            <button type="button" id="calculateButton">Calculate Royalties</button>
        </form>
        <div id="result" class="result-box hidden"></div>
    </div>
    <?php
    return ob_get_clean();
}

function kdp_royalty_calculator_html() {
    ob_start();
    ?>
    <div class="royalty-calculator" id="kdp-calculator">
        <h2>KDP Royalties Calculator</h2>
        <form id="kdpRoyaltyForm">
            <label for="kdpRoyaltyType">Royalty Type:</label>
            <select id="kdpRoyaltyType" name="kdpRoyaltyType">
                <option value="70">70% Royalty</option>
                <option value="35">35% Royalty</option>
            </select>

            <label for="kdpBookPrice">Book Price:</label>
            $<input type="number" id="kdpBookPrice" name="kdpBookPrice" value="2.99" min="0.99" step="0.01" required>

            <label for="kdpFileSize">File Size (MB):</label>
            <input type="number" id="kdpFileSize" name="kdpFileSize" value="1" min="0" step="0.1" required>

            <label for="kdpSalesQuantity">Number of Books Sold:</label>
            <input type="number" id="kdpSalesQuantity" name="kdpSalesQuantity" value="1000" min="1" step="1" required>

            <button type="button" id="kdpCalculateButton">Calculate KDP Royalties</button>
        </form>
        <div id="kdpResult" class="result-box hidden"></div>
    </div>
    <?php
    return ob_get_clean();
}