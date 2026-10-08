const BULAN = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sept', 'Okt', 'Nov', 'Des'];

/** "2026-09-25" -> "25 Sept 2026" */
export function formatTanggal(iso: string): string {
    const [y, m, d] = iso.slice(0, 10).split('-').map(Number);

    return y && m && d ? `${d} ${BULAN[m - 1]} ${y}` : iso;
}

export function formatUkuran(bytes: number): string {
    return bytes >= 1024 * 1024
        ? `${(bytes / 1024 / 1024).toFixed(1)} MB`
        : `${Math.max(1, Math.round(bytes / 1024))} KB`;
}

/** Tanggal hari ini (zona waktu lokal) dalam format YYYY-MM-DD. */
export function hariIni(): string {
    const n = new Date();
    const p = (v: number) => String(v).padStart(2, '0');

    return `${n.getFullYear()}-${p(n.getMonth() + 1)}-${p(n.getDate())}`;
}

/** 10000 -> "10.000", 1.5 -> "1,5" (format Indonesia). */
export function formatAngka(value: number, maxFraction = 2): string {
    return new Intl.NumberFormat('id-ID', { maximumFractionDigits: maxFraction }).format(value);
}

/** "10.000" -> 10000, "1,5" -> 1.5, "1.5" -> 1.5. Mengembalikan null bila bukan angka. */
export function parseAngka(text: string): number | null {
    const s = text.trim().replace(/\s/g, '');

    if (!s) {
        return null;
    }

    const n = /^\d{1,3}(\.\d{3})+(,\d+)?$/.test(s)
        ? Number(s.replace(/\./g, '').replace(',', '.'))
        : Number(s.replace(',', '.'));

    return Number.isFinite(n) && n >= 0 ? n : null;
}

/** Ton dengan jumlah desimal menyesuaikan besarnya angka: 12840 -> "12.840", 12.5 -> "12,5". */
export function formatTon(value: number): string {
    return formatAngka(value, value >= 100 ? 0 : value >= 1 ? 1 : 2);
}
