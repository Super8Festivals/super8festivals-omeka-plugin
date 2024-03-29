import Rest from "../../admin/javascripts/utils/rest.js";

export const HTTPRequestMethod = {
    GET: "GET",
    POST: "POST",
    PUT: "PUT",
    DELETE: "DELETE",
};

export default class API {

    static constructURL(parts) {
        return `/rest-api/${parts.join("/")}/`;
    }

    /**
     * @param url
     * @param {string} method
     * @param {FormData | null} formData
     * @returns {Promise<void>}
     */
    static async performRequest(url, method, formData = null) {
        switch (method) {
            default:
                return null;
            case HTTPRequestMethod.GET:
                return Rest.get(url);
            case HTTPRequestMethod.POST:
                return Rest.post(url, formData);
            case HTTPRequestMethod.PUT:
                return Rest.post(url, formData);
            case HTTPRequestMethod.DELETE:
                return Rest.delete(url);
        }
    }

}
