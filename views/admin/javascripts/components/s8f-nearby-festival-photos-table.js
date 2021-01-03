import { html } from "../../../shared/javascripts/vendor/lit-html.js";
import { component, useEffect, useState } from "../../../shared/javascripts/vendor/haunted.js";

import Alerts from "../utils/alerts.js";
import API, { HTTPRequestMethod } from "../../../shared/javascripts/api.js";
import Modals from "../utils/modals.js";
import { FormAction, scrollTo, SUPPORTED_IMAGE_MIMES } from "../../../shared/javascripts/misc.js";
import { Person } from "../utils/s8f-records.js";
import _ from "../../../shared/javascripts/vendor/lodash.js";


function NearbyFestivalPhotosTable(element) {
    const [allContributors, setAllContributors] = useState();
    const [photos, setPhotos] = useState();
    const [modalTitle, setModalTitle] = useState();
    const [modalBody, setModalBody] = useState();

    const fetchPhotos = async () => {
        try {
            const photos = await API.performRequest(API.constructURL([
                "countries",
                element.countryId,
                "cities",
                element.cityId,
                "nearby-festivals",
                element.festivalId,
                "photos",
            ]), HTTPRequestMethod.GET);
            setPhotos(photos);
        } catch (err) {
            Alerts.error("alerts", html`<strong>Error</strong> - Failed to Fetch Festival Photos`, err);
            console.error(`Error - Failed to Fetch Festival Photos: ${err.message}`);
        }
    };

    const fetchAllContributors = async () => {
        try {
            const contributors = await API.performRequest(API.constructURL([
                "contributors",
            ]), HTTPRequestMethod.GET);
            setAllContributors(_.orderBy(contributors, ["person.first_name", "person.last_name", "person.organization_name"]));
        } catch (err) {
            Alerts.error("alerts", html`<strong>Error</strong> - Failed to Fetch Contributors`, err);
            console.error(`Error - Failed to Fetch Contributors: ${err.message}`);
        }
    };

    useEffect(() => {
        fetchPhotos();
        fetchAllContributors();
    }, []);

    const performRestAction = async (formData, action) => {
        let promise = null;
        switch (action) {
            case FormAction.Add:
                promise = API.performRequest(API.constructURL([
                    "countries",
                    element.countryId,
                    "cities",
                    element.cityId,
                    "nearby-festivals",
                    element.festivalId,
                    "photos",
                ]), HTTPRequestMethod.POST, formData);
                break;
            case FormAction.Update:
                promise = API.performRequest(API.constructURL([
                    "countries",
                    element.countryId,
                    "cities",
                    element.cityId,
                    "nearby-festivals",
                    element.festivalId,
                    "photos",
                    formData.get("id"),
                ]), HTTPRequestMethod.POST, formData);
                break;
            case FormAction.Delete:
                promise = API.performRequest(API.constructURL([
                    "countries",
                    element.countryId,
                    "cities",
                    element.cityId,
                    "nearby-festivals",
                    element.festivalId,
                    "photos",
                    formData.get("id"),
                ]), HTTPRequestMethod.DELETE);
                break;
        }

        let actionVerb = action === FormAction.Add ? "added" : action === FormAction.Update ? "updated" : "deleted";
        let successMessage = `Successfully ${actionVerb} photo.`;

        try {
            const result = await promise;
            Alerts.success(
                "alerts",
                html`<strong>Success</strong>`,
                successMessage,
                false,
                3000,
            );
            await fetchPhotos();
        } catch (err) {
            Alerts.error("alerts", html`<strong>Error</strong>`, err);
        } finally {
            scrollTo("alerts");
        }
    };

    const cancelForm = () => {
        Modals.hide_custom("photos-form-modal");
    };

    const submitForm = (formData, action) => {
        performRestAction(formData, action).then(() => {
            Modals.hide_custom("photos-form-modal");
        });
    };

    const validateForm = (formData) => {
        // const first_name = formData.get("first_name");
        // if (first_name.replace(/\s/g, "") === "") {
        //     return { input_name: "name", message: "First Name can not be blank!" };
        // }
        return null;
    };

    const recordIdElementObj = (record) => ({ type: "text", name: "id", value: record ? record.id : null, visible: false });
    const getFormElements = (action, photo = null) => {
        let results = [];
        if (photo) {
            results = [...results, recordIdElementObj(photo)];
        }
        if (action === FormAction.Add || action === FormAction.Update) {
            results = [...results,
                { label: "Title", type: "text", name: "title", placeholder: "", value: photo ? photo.file.title : "" },
                { label: "Description", type: "text", name: "description", placeholder: "", value: photo ? photo.file.description : "" },
                {
                    label: "Contributor", name: "contributor_id", type: "select", options: ([{ id: 0 }, ...allContributors]).map((contributor) => {
                        return {
                            value: contributor.id,
                            label: contributor.id === 0 ? `None` : `${Person.getDisplayName(contributor.person)}`,
                            selected: photo ? photo.file.contributor_id === contributor.id : false,
                        };
                    }),
                },
                { label: "File", type: "file", name: "file", accept: SUPPORTED_IMAGE_MIMES.join(", ") },
            ];
        } else if (action === FormAction.Delete) {
            results = [...results,
                { type: "description", value: "Are you sure you want to delete this?" },
                { type: "description", styleClasses: ["text-danger"], value: "Warning: this can not be undone." },
            ];
        }
        return results;
    };

    const getForm = (action, record = null) => {
        return html`
            <s8f-form
                form-id="photo-form"
                .elements=${getFormElements(action, record)}
                .validateFunc=${action !== FormAction.Delete ? validateForm : undefined}
                .resetOnSubmit=${action === FormAction.Add}
                @cancel=${cancelForm}
                @submit=${(e) => { submitForm(e.detail, action); }}
            >
            </s8f-form>
        `;
    };

    const btnAddClick = () => {
        setModalTitle("Add Photo");
        setModalBody(getForm(FormAction.Add, null));
        Modals.show_custom("photos-form-modal");
        Alerts.clear("form-alerts");
    };

    const btnEditClick = (photo) => {
        setModalTitle("Edit Photo");
        setModalBody(getForm(FormAction.Update, photo));
        Modals.show_custom("photos-form-modal");
        Alerts.clear("form-alerts");
    };

    const btnDeleteClick = (photo) => {
        setModalTitle("Delete Photo");
        setModalBody(getForm(FormAction.Delete, photo));
        Modals.show_custom("photos-form-modal");
        Alerts.clear("form-alerts");
    };


    const tableColumns = [
        { title: "ID", accessor: "id" },
        { title: "Preview", accessor: "file" },
        { title: "Title", accessor: "file.title" },
        { title: "Description", accessor: "file.description" },
    ];

    return html`
        <s8f-modal
            modal-id="photos-form-modal"
            .modal-title=${modalTitle}
            .modal-body=${modalBody}
        >
        </s8f-modal>
        <h2 class="mb-4">
            Photos
            <button
                type="button"
                class="btn btn-success btn-sm"
                @click=${() => { btnAddClick(); }}
            >
                Add Photo
            </button>
        </h2>
        <s8f-records-table
            id="photo-table"
            .tableColumns=${tableColumns}
            .tableRows=${photos}
            .rowEditFunc=${(record) => { btnEditClick(record); }}
            .rowDeleteFunc=${(record) => { btnDeleteClick(record); }}
        >
        </s8f-records-table>
    `;
}

NearbyFestivalPhotosTable.observedAttributes = [
    "country-id",
    "city-id",
    "festival-id",
];

customElements.define("s8f-nearby-festival-photos-table", component(NearbyFestivalPhotosTable, { useShadowDOM: false }));