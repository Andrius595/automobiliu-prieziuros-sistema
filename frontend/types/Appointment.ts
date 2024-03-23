import type {Car} from "~/types/Car";
import type {Service} from "~/types/Service";
import type {Record} from "~/types/Record";

export type Appointment = {
    id: number;
    car_id: number|null;
    car: Car|undefined;
    service: Service|undefined;
    records: Record[]|undefined
    current_mi: number;
    completed_at: string|null;
    confirmed_at: string|null;
    created_at: string|null;
    updated_at: string|null;
}