export type ServiceReview = {
    id: number;
    appointment_id: number;
    user_id: number;
    rating: number;
    comment: string|null;
    created_at: string|null;
    updated_at: string|null;
}