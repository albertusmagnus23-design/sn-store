

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

window.Alpine = Alpine;

document.addEventListener('submit', async (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement) || !form.matches('[data-confirm-delete]') || form.dataset.confirmed === 'true') {
        return;
    }

    event.preventDefault();

    const resource = form.dataset.confirmResource ?? 'kategori';
    const name = form.dataset.confirmName ?? form.dataset.categoryName ?? 'item ini';
    const capitalizedResource = resource.charAt(0).toUpperCase() + resource.slice(1);

    const result = await Swal.fire({
        icon: 'warning',
        title: `Hapus ${resource}?`,
        text: `${capitalizedResource} "${name}" akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.`,
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#be123c',
        cancelButtonColor: '#64748b',
        reverseButtons: true,
        focusCancel: true,
    });

    if (result.isConfirmed) {
        form.dataset.confirmed = 'true';
        form.requestSubmit();
    }
});

document.querySelectorAll('[data-checkout-regions]').forEach((container) => {
    const province = container.querySelector('[name="province_code"]');
    const city = container.querySelector('[name="city_code"]');
    const district = container.querySelector('[name="district_code"]');
    const village = container.querySelector('[name="village_code"]');
    const error = container.querySelector('#region-load-error');
    const placeholders = new Map([
        [city, 'Pilih kabupaten/kota'],
        [district, 'Pilih kecamatan'],
        [village, 'Pilih desa/kelurahan'],
    ]);

    const reset = (select) => {
        select.replaceChildren(new Option(placeholders.get(select), ''));
        select.disabled = true;
    };

    const loadOptions = async (select, endpoint, code, selectedCode = '') => {
        reset(select);

        if (!code) {
            return;
        }

        const response = await fetch(endpoint.replace('__CODE__', encodeURIComponent(code)), {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error('Wilayah tidak dapat dimuat.');
        }

        const result = await response.json();

        if (!Array.isArray(result.data)) {
            throw new Error('Data wilayah tidak valid.');
        }

        result.data.forEach(({ code: optionCode, name }) => {
            select.add(new Option(name, optionCode));
        });
        select.disabled = result.data.length === 0;

        if (selectedCode && result.data.some((option) => option.code === selectedCode)) {
            select.value = selectedCode;
        }
    };

    const showLoadError = (loadError) => {
        reset(city);
        reset(district);
        reset(village);
        error.textContent = `${loadError.message} Muat ulang halaman atau coba lagi.`;
        error.classList.remove('hidden');
    };

    const loadCities = async (provinceCode, selectedCity = '', selectedDistrict = '', selectedVillage = '') => {
        error.classList.add('hidden');
        reset(city);
        reset(district);
        reset(village);

        if (!provinceCode) {
            return;
        }

        try {
            await loadOptions(city, container.dataset.citiesUrl, provinceCode, selectedCity);

            if (city.value) {
                await loadDistricts(city.value, selectedDistrict, selectedVillage);
            }
        } catch (loadError) {
            showLoadError(loadError);
        }
    };

    const loadDistricts = async (cityCode, selectedDistrict = '', selectedVillage = '') => {
        reset(district);
        reset(village);

        if (!cityCode) {
            return;
        }

        await loadOptions(district, container.dataset.districtsUrl, cityCode, selectedDistrict);

        if (district.value) {
            await loadVillages(district.value, selectedVillage);
        }
    };

    const loadVillages = async (districtCode, selectedVillage = '') => {
        reset(village);

        if (districtCode) {
            await loadOptions(village, container.dataset.villagesUrl, districtCode, selectedVillage);
        }
    };

    province.addEventListener('change', () => loadCities(province.value));
    city.addEventListener('change', async () => {
        reset(district);
        reset(village);

        if (city.value) {
            try {
                await loadDistricts(city.value);
            } catch (loadError) {
                showLoadError(loadError);
            }
        }
    });
    district.addEventListener('change', async () => {
        reset(village);

        if (district.value) {
            try {
                await loadVillages(district.value);
            } catch (loadError) {
                showLoadError(loadError);
            }
        }
    });

    if (province.value) {
        loadCities(
            province.value,
            container.dataset.selectedCity,
            container.dataset.selectedDistrict,
            container.dataset.selectedVillage,
        );
    }
});

document.querySelectorAll('[data-checkout-shipping]').forEach((form) => {
    const calculateButton = form.querySelector('[data-shipping-calculate]');
    const optionsContainer = form.querySelector('[data-shipping-options]');
    const errorContainer = form.querySelector('[data-shipping-error]');
    const feeContainer = form.querySelector('[data-shipping-fee]');
    const totalContainer = form.querySelector('[data-checkout-total]');
    const quoteIdInput = form.querySelector('[name="shipping_quote_id"]');
    const optionIdInput = form.querySelector('[name="shipping_option"]');
    const regionFields = ['province_code', 'city_code', 'district_code', 'village_code', 'postal_code']
        .map((name) => form.querySelector(`[name="${name}"]`));
    const csrfToken = form.querySelector('[name="_token"]')?.value;
    const subtotal = Number.parseInt(form.dataset.subtotal, 10);
    let selectedCost = 0;
    let hasSelectedOption = false;

    const formatRupiah = (amount) => `Rp ${new Intl.NumberFormat('id-ID').format(amount)}`;

    const updateTotal = () => {
        totalContainer.textContent = formatRupiah(subtotal + selectedCost);
        feeContainer.textContent = hasSelectedOption ? formatRupiah(selectedCost) : 'Pilih layanan';
    };

    const clearQuote = () => {
        quoteIdInput.value = '';
        optionIdInput.value = '';
        optionsContainer.replaceChildren();
        selectedCost = 0;
        hasSelectedOption = false;
        updateTotal();
    };

    const showError = (message) => {
        errorContainer.textContent = message;
        errorContainer.classList.remove('hidden');
    };

    const clearError = () => {
        errorContainer.textContent = '';
        errorContainer.classList.add('hidden');
    };

    const renderOptions = (quote) => {
        quoteIdInput.value = quote.quote_id;
        optionsContainer.replaceChildren();

        quote.options.forEach((option) => {
            const label = document.createElement('label');
            label.className = 'flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-3 text-sm transition hover:border-rose-300';

            const input = document.createElement('input');
            input.type = 'radio';
            input.name = 'shipping_option_choice';
            input.value = option.option_id;
            input.required = true;
            input.className = 'mt-1 border-slate-300 text-rose-700 focus:ring-rose-500';

            const details = document.createElement('span');
            details.className = 'min-w-0 flex-1';
            const title = document.createElement('span');
            title.className = 'block font-semibold text-slate-900';
            title.textContent = `${option.name} ${option.service}`;
            const description = document.createElement('span');
            description.className = 'mt-0.5 block text-xs text-slate-500';
            description.textContent = option.description || (option.etd ? `Estimasi ${option.etd} hari` : 'Estimasi pengiriman mengikuti kurir');
            details.append(title, description);

            const price = document.createElement('span');
            price.className = 'shrink-0 font-bold text-slate-900';
            price.textContent = formatRupiah(option.cost);

            label.append(input, details, price);
            input.addEventListener('change', () => {
                optionsContainer.querySelectorAll('label').forEach((optionLabel) => {
                    optionLabel.classList.remove('border-rose-400', 'bg-rose-50');
                });
                label.classList.add('border-rose-400', 'bg-rose-50');
                optionIdInput.value = option.option_id;
                selectedCost = option.cost;
                hasSelectedOption = true;
                updateTotal();
            });
            optionsContainer.append(label);
        });
    };

    calculateButton.addEventListener('click', async () => {
        clearError();
        clearQuote();

        const values = Object.fromEntries(regionFields.map((field) => [field.name, field.value]));

        if (Object.values(values).some((value) => value === '')) {
            showError('Lengkapi provinsi, kabupaten/kota, kecamatan, desa/kelurahan, dan kode pos terlebih dahulu.');
            return;
        }

        calculateButton.disabled = true;
        calculateButton.textContent = 'Menghitung ongkir...';

        try {
            const response = await fetch(form.dataset.shippingUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(values),
            });
            const result = await response.json();

            if (!response.ok) {
                const validationMessage = result.errors ? Object.values(result.errors).flat()[0] : null;
                throw new Error(validationMessage || result.message || 'Ongkos kirim tidak dapat dihitung. Coba lagi.');
            }

            if (!result.quote_id || !Array.isArray(result.options) || result.options.length === 0) {
                throw new Error('Kurir tidak menyediakan layanan untuk alamat ini.');
            }

            renderOptions(result);
        } catch (quoteError) {
            showError(quoteError.message || 'Layanan ongkir sedang tidak tersedia. Coba lagi.');
        } finally {
            calculateButton.disabled = false;
            calculateButton.textContent = 'Cek ongkos kirim';
        }
    });

    regionFields.forEach((field) => {
        field.addEventListener('change', () => {
            clearQuote();
            clearError();
        });
        field.addEventListener('input', () => {
            clearQuote();
            clearError();
        });
    });

    form.addEventListener('submit', (event) => {
        if (!quoteIdInput.value || !optionIdInput.value) {
            event.preventDefault();
            showError('Hitung ongkos kirim dan pilih layanan pengiriman sebelum membuat pesanan.');
        }
    });

    updateTotal();
});

Alpine.start();
