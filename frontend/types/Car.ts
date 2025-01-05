import type {User} from "~/types/User";

export type Car = {
    id: number;
    owner_id: number|null;
    owner: User|undefined;
    make: string;
    model: string;
    vin: string;
    plate_no: string|null;
    color: string|null;
    mileage_type: number;
    year_of_manufacture: number|null;
    created_at: string|null;
    updated_at: string|null;
    logo_path: string|null;
}