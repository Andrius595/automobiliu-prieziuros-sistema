import type {VDataTable} from "vuetify/components";

export type DatatableSortBy = VDataTable['$props']['sortBy']
export type ReadonlyHeaders = VDataTable['$props']['headers']
export type DatatablesOptions = {
    page: number;
    itemsPerPage: number;
    sortBy: DatatableSortBy;
    groupBy?: DatatableSortBy;
    search?: string;
}