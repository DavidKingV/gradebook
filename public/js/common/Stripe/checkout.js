// This is your test publishable API key.
const phpPath = "../backend/payments/Stripe/create.php";
const stripe = Stripe("pk_test_51IwCv5IQorR0pamGRJVw2B4OlmKVEMYj0zhLOUyuGLKRX2HHPdFPTwUFnMKFiInYcPy3Y6nSq4P4TQc6RvDIta9g00yEm58lYJ");

initialize();

// Create a Checkout Session
async function initialize() {
  const fetchClientSecret = async () => {
    const response = await fetch(phpPath, {
      method: "POST",
    });
    const { clientSecret } = await response.json();
    return clientSecret;
  };

  const checkout = await stripe.initEmbeddedCheckout({
    fetchClientSecret,
  });

  // Mount Checkout
  checkout.mount('#checkout');
}