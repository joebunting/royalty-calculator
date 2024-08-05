jQuery(document).ready(function($) {
    console.log("Royalty calculator script loaded");

    if (window.location.hostname !== 'thewritepractice.com') {
        $(".twp-attribution").removeClass('hidden');
    }

    function calculateRoyalties(formId, resultId, calculatorType) {
        console.log("Calculating royalties for " + calculatorType);
        var form = $("#" + formId);
        var data = form.serializeArray();
        data.push({name: "action", value: "calculate_royalties"});
        data.push({name: "calculatorType", value: calculatorType});

        console.log("Form data:", data);

        $.ajax({
            url: royaltyCalculatorAjax.ajaxurl,
            type: "POST",
            data: data,
            success: function(response) {
                console.log("AJAX success:", response);
                if (response.success) {
                    $("#" + resultId)
                        .removeClass('hidden')
                        .html(
                            "<h3>Results:</h3>" +
                            "<p><i class='fas fa-money-bill-wave'></i> Estimated Total Royalties: <span class='result-value'>$" + response.data.totalRoyalty + "</span></p>" +
                            "<p><i class='fas fa-book'></i> Royalty Per Book: <span class='result-value'>$" + response.data.royaltyPerBook + "</span></p>" +
                            "<p>Master book marketing with the <a href='https://thewritepractice.com/resources/book-sales-tactics/' target='_blank'>Book Sales Tactics class here</a>."
                        );
                } else {
                    $("#" + resultId)
                        .removeClass('hidden')
                        .html("<p class='error'>Error calculating royalties.</p>");
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                console.log("XHR:", xhr);
                $("#" + resultId)
                    .removeClass('hidden')
                    .html("<p class='error'>Error: Unable to calculate royalties. Please try again later.</p>");
            }
        });
    }

    $(document).on("click", "#calculateButton", function(e) {
        console.log("Calculate button clicked");
        e.preventDefault();
        calculateRoyalties("royaltyForm", "result", "general");
    });

    $(document).on("click", "#kdpCalculateButton", function(e) {
        console.log("KDP Calculate button clicked");
        e.preventDefault();
        calculateRoyalties("kdpRoyaltyForm", "kdpResult", "kdp");
    });

    function hideResults() {
        $("#kdpResult, #result").addClass('hidden').html('');
    }
    
    // Add event listeners to all input fields and select elements
    $(".royalty-calculator input, .royalty-calculator select").on('change', hideResults);
});