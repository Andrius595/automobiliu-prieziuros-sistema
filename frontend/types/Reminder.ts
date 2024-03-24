export type Reminder = {
    id: number;
    car_id: number;
    title: string;
    description: string|null;
    interval: number;
    type: number; // ??
    last_reminded_at: string|null;
    created_at: string|null;
    updated_at: string|null;
}