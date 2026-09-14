let isExtracting = false;
let autofilledFields = [];

document.addEventListener('DOMContentLoaded', function () {
    const extractBtn = document.getElementById('extractDataBtn');
    if (extractBtn) {
        extractBtn.addEventListener('click', extractDataFromDocument);
    }
});

async function extractDataFromDocument() {
    if (isExtracting) return;

    const files = (window.beneficiaryFiles && window.beneficiaryFiles.length > 0)
        ? window.beneficiaryFiles
        : (document.getElementById('beneficiary_file')?.files
            ? Array.from(document.getElementById('beneficiary_file').files)
            : []);

    if (files.length === 0) {
        showExtractMessage('Please select at least one file first', 'warning');
        return;
    }

    const imageFiles = files.filter(f =>
        ['image/jpeg', 'image/png', 'image/gif', 'image/webp'].includes(f.type)
    );

    if (imageFiles.length === 0) {
        showExtractMessage('Auto-fill requires JPG/PNG images. Upload the intake sheet page(s) as images.', 'warning');
        return;
    }

    isExtracting = true;
    setExtractLoading(true);
    hideVerifyBanner();

    const formData = new FormData();
    for (const file of imageFiles) {
        formData.append('document[]', file);
    }
    formData.append('program_id', document.querySelector('[name="program_id"]')?.value || '');

    const csrfToken = document.querySelector('[name="_token"]')?.value;

    try {
        const res = await fetch('/beneficiaries/parse-document', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: formData,
        });

        if (!res.ok) {
            const err = await res.json().catch(() => ({ message: 'Failed to parse document' }));
            throw new Error(err.message || 'Failed to parse document');
        }

        const data = await res.json();
        autofilledFields = [];
        fillAllFormFields(data);
        showVerifyBanner(autofilledFields.length);
        if (typeof initCivilStatus === 'function') initCivilStatus();

        switchTab('beneficiary');
        window.scrollTo({ top: 0, behavior: 'smooth' });

    } catch (e) {
        showExtractMessage(e.message || 'Could not parse document. Please fill the form manually.', 'error');
    } finally {
        isExtracting = false;
        setExtractLoading(false);
    }
}

function fillAllFormFields(data) {
    setFieldValue('first_name', data.first_name);
    setFieldValue('middle_name', data.middle_name);
    setFieldValue('last_name', data.last_name);
    setFieldValue('name_extension', data.name_extension);
    setFieldValue('birth_date', data.birth_date);
    if (data.sex) setRadioValue('sex', data.sex);
    setFieldValue('email', data.email);
    setFieldValue('contact_number', data.contact_number);
    setFieldValue('contact_number_code', data.dial_code || '+63');
    setFieldValue('address_line', data.address_line);
    setFieldValue('country', data.country);
    setFieldValue('province', data.province);
    setFieldValue('city', data.city);
    setFieldValue('zip', data.zip);
    setCheckboxValue('form_given', data.form_given);
    setCheckboxValue('with_disability', data.with_disability);

    if (data.guardians && data.guardians.length > 0) {
        const container = document.getElementById('guardians-container');
        if (container) {
            const existingBlocks = container.querySelectorAll('.guardian-block');
            for (let i = 1; i < existingBlocks.length; i++) {
                existingBlocks[i].querySelector('.remove-guardian-btn')?.click();
            }
        }

        data.guardians.forEach((guardian, i) => {
            if (i > 0) {
                const addBtn = document.getElementById('add-guardian');
                if (addBtn) addBtn.click();
            }
            setFieldValue(`guardians[${i}][guardian_type]`, guardian.guardian_type);
            setFieldValue(`guardians[${i}][first_name]`, guardian.first_name);
            setFieldValue(`guardians[${i}][middle_name]`, guardian.middle_name);
            setFieldValue(`guardians[${i}][last_name]`, guardian.last_name);
            setFieldValue(`guardians[${i}][birth_date]`, guardian.birth_date);
            setFieldValue(`guardians[${i}][place_of_birth]`, guardian.place_of_birth);
            setFieldValue(`guardians[${i}][address_line]`, guardian.address_line);
            setFieldValue(`guardians[${i}][city]`, guardian.city);
            setFieldValue(`guardians[${i}][province]`, guardian.province);
            setFieldValue(`guardians[${i}][country]`, guardian.country);
            setFieldValue(`guardians[${i}][zip]`, guardian.zip);
            if (guardian.sex) setRadioValue(`guardians[${i}][sex]`, guardian.sex);
            setFieldValue(`guardians[${i}][civil_status]`, guardian.civil_status);
            setFieldValue(`guardians[${i}][contact_number]`, guardian.contact_number);
            setFieldValue(`guardians[${i}][dial_code]`, guardian.dial_code || '+63');
            setFieldValue(`guardians[${i}][highest_education]`, guardian.highest_education);
            setFieldValue(`guardians[${i}][job]`, guardian.job);
            setFieldValue(`guardians[${i}][estimated_salary]`, guardian.estimated_salary);
        });
    }

    if (data.intake) {
        Object.entries(data.intake).forEach(([key, value]) => {
            if (value !== null && value !== undefined) {
                const el = document.querySelector(`[name="intake[${key}]"]`);
                if (el) {
                    if (el.type === 'checkbox') {
                        el.checked = !!value;
                    } else {
                        el.value = value;
                    }
                    markAutofilled(el);
                }
            }
        });
    }
}

function markAutofilled(el) {
    if (!el || el.dataset.autofilled === 'true') return;
    el.dataset.autofilled = 'true';
    autofilledFields.push(el);

    el.style.borderColor = 'var(--color-accent1)';
    el.style.borderWidth = '2px';

    el.addEventListener('focus', clearAutofilledHighlight, { once: true });
    el.addEventListener('change', clearAutofilledHighlight, { once: true });
}

function clearAutofilledHighlight(e) {
    const el = e.target;
    el.dataset.autofilled = '';
    el.style.borderColor = '';
    el.style.borderWidth = '';
    const idx = autofilledFields.indexOf(el);
    if (idx > -1) autofilledFields.splice(idx, 1);
    if (autofilledFields.length === 0) hideVerifyBanner();
}

function setFieldValue(name, value) {
    if (value === null || value === undefined) return;
    const el = document.querySelector(`[name="${name}"]`);
    if (el) {
        if (el.type === 'checkbox') {
            el.checked = !!value;
        } else if (el.type === 'radio') {
            setRadioValue(name, value);
            return;
        } else if (el.tagName === 'SELECT') {
            el.value = value;
        } else {
            el.value = value;
        }
        markAutofilled(el);
    }
}

function setRadioValue(name, value) {
    if (!value) return;
    const el = document.querySelector(`[name="${name}"][value="${value}"]`);
    if (el) {
        el.checked = true;
        markAutofilled(el);
    }
}

function setCheckboxValue(name, value) {
    const el = document.querySelector(`[name="${name}"]`);
    if (el) {
        el.checked = !!value;
        markAutofilled(el);
    }
}

function showVerifyBanner(count) {
    const banner = document.getElementById('verifyAutofillBanner');
    if (!banner) return;
    banner.classList.remove('hidden');
    document.getElementById('verifyAutofillCount').textContent = count;
}

function hideVerifyBanner() {
    const banner = document.getElementById('verifyAutofillBanner');
    if (banner) banner.classList.add('hidden');
}

function setExtractLoading(isLoading) {
    const btn = document.getElementById('extractDataBtn');
    const btnText = document.getElementById('extractBtnText');
    const spinner = document.getElementById('extractBtnSpinner');

    if (!btn) return;

    btn.disabled = isLoading;
    if (btnText) btnText.classList.toggle('hidden', isLoading);
    if (spinner) spinner.classList.toggle('hidden', !isLoading);
}

function showExtractMessage(message, type) {
    const status = document.getElementById('extractStatus');
    if (!status) return;

    status.classList.remove('hidden', 'text-green-400', 'text-yellow-400', 'text-red-400');

    const colors = {
        success: 'text-green-400',
        warning: 'text-yellow-400',
        error: 'text-red-400',
    };

    status.classList.add(colors[type] || 'text-white');
    status.textContent = message;
}
