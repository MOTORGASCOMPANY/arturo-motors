// helpers.js - Utilidades compartidas

/**
 * Formatea número con separador de miles
 * @param {number|string} n
 * @returns {string}
 */
export function formatearNumero(n) {
    if (n === null || n === undefined || n === '') return '';
    const num = Number(n);
    if (isNaN(num)) return String(n);
    return num.toLocaleString('es-AR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
}

/**
 * Formatea moneda (ARS)
 * @param {number|string} n
 * @returns {string}
 */
export function formatearMoneda(n) {
    if (n === null || n === undefined || n === '') return '';
    const num = Number(n);
    if (isNaN(num)) return String(n);
    return new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS', minimumFractionDigits: 0 }).format(num);
}

/**
 * Formatea fecha a dd/mm/yyyy
 * @param {string|Date} fecha
 * @returns {string}
 */
export function formatearFecha(fecha) {
    if (!fecha) return '';
    const d = fecha instanceof Date ? fecha : new Date(fecha);
    if (isNaN(d.getTime())) return '';
    return d.toLocaleDateString('es-AR', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

/**
 * Debounce
 * @param {Function} fn
 * @param {number} ms
 * @returns {Function}
 */
export function debounce(fn, ms) {
    let timer;
    return function (...args) {
        clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), ms);
    };
}

/**
 * Obtiene CSRF token de meta tag
 * @returns {string}
 */
export function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.getAttribute('content') : '';
}

/**
 * Fetch con CSRF automático
 * @param {string} url
 * @param {object} options
 * @returns {Promise<Response>}
 */
export async function fetchWithCsrf(url, options = {}) {
    const headers = new Headers(options.headers || {});
    headers.set('X-CSRF-TOKEN', getCsrfToken());
    headers.set('Accept', 'application/json');
    if (!headers.has('Content-Type') && options.body && !(options.body instanceof FormData)) {
        headers.set('Content-Type', 'application/json');
    }
    return fetch(url, { ...options, headers });
}