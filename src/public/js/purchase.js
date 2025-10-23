document.addEventListener('DOMContentLoaded', () => {
	const sel = document.getElementById('payment');
	const out = document.getElementById('paymentSummary');
	if (!sel || !out) return;

	const dict = { konbini: 'コンビニ払い', card: 'カード支払い' };
	const update = () => { out.textContent = dict[sel.value] || ''; };
	update();
	sel.addEventListener('change', update);
});
