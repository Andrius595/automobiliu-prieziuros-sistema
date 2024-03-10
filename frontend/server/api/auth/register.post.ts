import {H3Event} from "h3";
import { $fetch } from "ofetch";

export default defineEventHandler(async (event: H3Event) => {
    const body = await readBody(event)

    return await $fetch(useRuntimeConfig().public.apiURL + '/auth/register', {
        method: 'POST',
        body: body,
        headers: {
            Accept: 'application/json',
        }
    })
})