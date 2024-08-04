<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function calculate_royalties() {
    $calculatorType = $_POST['calculatorType'];

    if ($calculatorType === 'kdp') {
        $royaltyType = floatval($_POST["kdpRoyaltyType"]) / 100;
        $bookPrice = floatval($_POST["kdpBookPrice"]);
        $fileSize = floatval($_POST["kdpFileSize"]);
        $salesQuantity = intval($_POST["kdpSalesQuantity"]);

        $deliveryCost = $fileSize * 0.15; // $0.15 per MB for delivery cost
        
        if ($royaltyType == 0.7) {
            $royaltyPerBook = ($bookPrice * 0.7) - $deliveryCost;
        } else {
            $royaltyPerBook = $bookPrice * 0.35;
        }

        $totalRoyalty = $royaltyPerBook * $salesQuantity;
    } else {
        $royaltyPercentage = floatval($_POST["royaltyPercentage"]) / 100;
        $bookPrice = floatval($_POST["bookPrice"]);
        $hasAgent = isset($_POST["hasAgent"]) ? $_POST["hasAgent"] === 'true' : false;
        $salesQuantity = intval($_POST["salesQuantity"]);

        $royaltyPerBook = $bookPrice * $royaltyPercentage;
        if ($hasAgent) {
            $royaltyPerBook *= 0.85; // Deduct 15% agent commission
        }

        $totalRoyalty = $royaltyPerBook * $salesQuantity;
    }

    wp_send_json_success(array(
        'totalRoyalty' => number_format($totalRoyalty, 2),
        'royaltyPerBook' => number_format($royaltyPerBook, 2)
    ));
}
add_action('wp_ajax_calculate_royalties', 'calculate_royalties');
add_action('wp_ajax_nopriv_calculate_royalties', 'calculate_royalties');