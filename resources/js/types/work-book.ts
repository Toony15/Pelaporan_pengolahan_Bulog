export type AttachmentCategory = 'video' | 'foto_gabah' | 'foto_mitra' | 'foto_ktp';

export type AttachmentInfo = {
    url: string;
    name: string;
    size: number;
    type: 'photo' | 'video';
};

export type WorkBookCard = {
    id: number;
    pic_name: string;
    mitra_pengolahan: string;
    village: string;
    regency: string;
    absorption_kg: number;
    absorption_date: string;
    video_count: number;
    photo_count: number;
    cover_url: string | null;
};

export type WorkBookDetail = {
    id: number;
    pic_name: string;
    mitra_pengolahan: string;
    village: string;
    regency: string;
    absorption_kg: number;
    absorption_date: string;
    attachments: Partial<Record<AttachmentCategory, AttachmentInfo>>;
};
