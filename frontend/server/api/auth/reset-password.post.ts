import {H3Event} from "h3";
import {$fetch} from "ofetch";

export default defineEventHandler(async (event: H3Event) => {
    const body = await readBody(event)

    console.log(body)

    const response = await $fetch(useRuntimeConfig().public.apiURL + '/auth/reset-password', {
        method: 'post',
        body: body,
        headers: {
            Accept: 'application/json',
        }
    })

    console.log(response)

    return response
})