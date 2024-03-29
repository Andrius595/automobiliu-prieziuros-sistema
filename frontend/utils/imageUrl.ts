import type { UseFetchOptions} from "nuxt/app";
import {serialize} from "object-to-formdata";

export default (path: string|null) =>
{
    if (!path) return '/no-image.png';
    const prefix = 'public/';
    return useRuntimeConfig().public.storageURL + (path.startsWith(prefix) ? path.slice(prefix.length) : path);
}