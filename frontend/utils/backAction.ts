import {serialize} from "object-to-formdata";
import {$fetch} from "ofetch";

export default async <T>(
    route: Parameters<typeof $fetch<T>>[0],
    options: Parameters<typeof $fetch<T>>[1] & {sendsFiles?: boolean}
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

    return $fetch<T>(useRuntimeConfig().public.apiURL + route, options);
}