export function RenderCheckout(preferencesId) {
    const mp = new MercadoPago('TEST-ac2a9cf5-bf80-4cbd-be85-111dc2e3fa2c', {
        locale: 'es-MX'
    });

    mp.bricks().create("wallet", "wallet_container", {
        initialization: {
            preferenceId: preferencesId,
            redirectMode: "modal",
        },
        customization: {
            text: {
                action: 'pay',
                valueProp: 'security_details',
            }
        },
        });
}