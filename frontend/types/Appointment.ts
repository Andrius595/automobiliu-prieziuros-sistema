import type {Car} from "~/types/Car";
import type {Service} from "~/types/Service";

export type Appointment = {
    id: number;
    car_id: number|null;
    car: Car|undefined;
    service: Service|undefined;
    completed_at: string|null;
    confirmed_at: string|null;
    created_at: string|null;
    updated_at: string|null;
}