export type User = {
    id: number;
    first_name: string;
    last_name: string;
    email: string;
    service_id: number|null;
    password?: string;
    created_at: string;
    updated_at: string;
    role?: string;
}