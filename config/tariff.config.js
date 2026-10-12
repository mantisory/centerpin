/*
 * US tariff fee settings — edit the three numbers below, save, and upload.
 *
 *   threshold           Order subtotal (in $) where the fee switches over.
 *   preThresholdTariff  Flat fee (in $) charged when the subtotal is BELOW the threshold.
 *   postThresholdTariff Percentage charged when the subtotal is AT OR ABOVE the threshold,
 *                       written as a whole number (50 = 50%, 25 = 25%).
 *
 * Example with the values below: a $40 order pays a flat $25.00,
 * a $120 order pays 50% = $60.00.
 *
 * Tip: to avoid a jump in price at the threshold, keep
 * threshold x postThresholdTariff / 100 = preThresholdTariff (50 x 50 / 100 = 25).
 */
const tariffConfig = {
  threshold: 100,
  preThresholdTariff: 30,
  postThresholdTariff: 32,
};
