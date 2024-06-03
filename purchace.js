const menu = document.getElementById('menu');
const jumlah = document.getElementById('jumlah');
const total = document.getElementById('total');
const minuman = document.getElementById('minuman');
const jumlah_minuman = document.getElementById('jumlah_minuman');

function calculateTotal() {
  const menuPrice = {
    'mie ayam bakso': 15000,
    'mie ayam jamur': 18000,
    'mie ayam pangsit': 13000,
    'mie ayam komplit': 18000,
  };
  const minumanPrice = {
    'susu': 6000,
    'teh': 5000,
    'es teh': 10000,
    'es kelapa': 10000,
  };
  const menuTotal = menuPrice[menu.value] * jumlah.value;
  const minumanTotal = minumanPrice[minuman.value] * jumlah_minuman.value;
  const totalPrice = menuTotal + minumanTotal;
  total.textContent = `Rp. ${totalPrice}`;
}

menu.addEventListener('change', calculateTotal);
jumlah.addEventListener('input', calculateTotal);
minuman.addEventListener('change', calculateTotal);
jumlah_minuman.addEventListener('input', calculateTotal);

