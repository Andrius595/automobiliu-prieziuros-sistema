import {serialize} from "object-to-formdata";
import {$fetch, type FetchOptions} from "ofetch";


export default async (
    route: string,
    options: FetchOptions & {sendsFiles?: boolean}
) =>
{
    const jwt = useJWT()

    let token = await jwt.getToken()

    if (!token) {
        await navigateTo('/login');
    }

    options.headers = {
        ...(options.headers || {}),
        Authorization: 'Bearer ' + token
    }
    if (options.sendsFiles) {
        (options.body as any)._method = options.method
        options.method = 'post'
        options.body = serialize(options.body)
    }
    if (!Object.hasOwn(options.headers as Object, 'Accept')) {
        options.headers = {
            ...options.headers,
            Accept: 'application/json'
        }
    }

    return $fetch(useRuntimeConfig().public.apiURL + route, options);
}