import { html } from "../../../shared/javascripts/vendor/lit-html.js";
import { component, useEffect, useState } from "../../../shared/javascripts/vendor/haunted.js";
import _ from "../../../shared/javascripts/vendor/lodash.js";

import Alerts from "../utils/alerts.js";
import API, { HTTPRequestMethod } from "../../../shared/javascripts/api.js";
import Modals from "../utils/modals.js";
import { FormAction, isEmptyString, openLink, scrollTo } from "../../../shared/javascripts/misc.js";
import { eventBus, S8FEvent } from "../../../shared/javascripts/event-bus.js";


function ContributorsTable() {
    const [contributors, setContributors] = useState();
    const [modalTitle, setModalTitle] = useState();
    const [modalBody, setModalBody] = useState();

    const fetchContributors = async () => {
        try {
            const contributors = await API.performRequest(API.constructURL(["contributors"]), HTTPRequestMethod.GET);
            setContributors(_.orderBy(contributors, ["person.first_name", "person.last_name"]));
        } catch (err) {
            Alerts.error("alerts", html`<strong>Error</strong> - Failed to Fetch Contributors`, err);
            console.error(`Error - Failed to Fetch Contributors: ${err.message}`);
        }
    };

    useEffect(() => {
        fetchContributors();
    }, []);

    const performRestAction = async (formData, action) => {
        let promise = null;
        switch (action) {
            case FormAction.Add:
                promise = API.performRequest(API.constructURL(["contributors"]), HTTPRequestMethod.POST, formData);
                break;
            case FormAction.Update:
                promise = API.performRequest(API.constructURL(["contributors", formData.get("id")]), HTTPRequestMethod.POST, formData);
                break;
            case FormAction.Delete:
                promise = API.performRequest(API.constructURL(["contributors", formData.get("id")]), HTTPRequestMethod.DELETE);
                break;
        }

        let actionVerb = action === FormAction.Add ? "added" : action === FormAction.Update ? "updated" : "deleted";
        let successMessage = `Successfully ${actionVerb} contributor.`;

        try {
            const result = await promise;
            Alerts.success(
                "alerts",
                html`<strong>Success</strong>`,
                successMessage,
                false,
                3000,
            );
            await fetchContributors();
        } catch (err) {
            Alerts.error("alerts", html`<strong>Error</strong>`, err);
        } finally {
            scrollTo("alerts");
        }
    };

    const cancelForm = () => {
        Modals.hide_custom("contributors-form-modal");
    };

    const submitForm = (formData, action) => {
        eventBus.dispatch(S8FEvent.RequestFormSubmit);
        performRestAction(formData, action).then(() => {
            Modals.hide_custom("contributors-form-modal");
        }).finally(() => {
            eventBus.dispatch(S8FEvent.CompleteFormSubmit);
        });
    };

    const validateForm = (formData) => {
        const first_name = formData.get("first_name");
        const last_name = formData.get("last_name");
        const organization_name = formData.get("organization_name");
        const email = formData.get("email");
        if (isEmptyString(first_name) && isEmptyString(last_name) && isEmptyString(organization_name) && isEmptyString(email)) {
            return { input_name: "name", message: "Either a name or email is required!" };
        }
        return null;
    };

    const recordIdElementObj = (record) => ({ type: "text", name: "id", value: record ? record.id : null, visible: false });
    const getFormElements = (action, contributor = null) => {
        let results = [];
        if (contributor) {
            results = [...results, recordIdElementObj(contributor)];
        }
        if (action === FormAction.Add || action === FormAction.Update) {
            results = [...results,
                { label: "First Name", type: "text", name: "first_name", placeholder: "", value: contributor ? contributor.person.first_name : "" },
                { label: "Last Name", type: "text", name: "last_name", placeholder: "", value: contributor ? contributor.person.last_name : "" },
                { label: "Organization Name", type: "text", name: "organization_name", placeholder: "", value: contributor ? contributor.person.organization_name : "" },
                { label: "Email", type: "text", name: "email", placeholder: "", value: contributor ? contributor.person.email : "" },
                { label: "Email Visible", type: "toggle", name: "is_email_visible", value: contributor ? contributor.person.is_email_visible : false },
                // { label: "Photo", type: "file", name: "file" },
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
                form-id="contributor-form"
                .elements=${getFormElements(action, record)}
                .validateFunc=${action !== FormAction.Delete ? validateForm : undefined}
                @cancel=${cancelForm}
                @submit=${(e) => { submitForm(e.detail, action); }}
            >
            </s8f-form>
        `;
    };

    const btnAddClick = () => {
        setModalTitle("Add Contributor");
        setModalBody(getForm(FormAction.Add, null));
        Modals.show_custom("contributors-form-modal");
        Alerts.clear("form-alerts");
    };

    const btnEditClick = (contributor) => {
        setModalTitle("Edit Contributor");
        setModalBody(getForm(FormAction.Update, contributor));
        Modals.show_custom("contributors-form-modal");
        Alerts.clear("form-alerts");
    };

    const btnDeleteClick = (contributor) => {
        setModalTitle("Delete Contributor");
        setModalBody(getForm(FormAction.Delete, contributor));
        Modals.show_custom("contributors-form-modal");
        Alerts.clear("form-alerts");
    };


    const tableColumns = [
        { title: "ID", accessor: "id" },
        // { title: "Preview", accessor: "file" },
        { title: "First Name", accessor: "person.first_name" },
        { title: "Last Name", accessor: "person.last_name" },
        { title: "Organization Name", accessor: "person.organization_name" },
        { title: "Email", accessor: "person.email" },
        { title: "Is Email Visible", accessor: "person.is_email_visible" },
    ];

    return html`
        <s8f-modal
            modal-id="contributors-form-modal"
            .modal-title=${modalTitle}
            .modal-body=${modalBody}
        >
        </s8f-modal>
        <h2 class="mb-4">
            Contributor
            <button
                type="button"
                class="btn btn-success btn-sm"
                @click=${() => { btnAddClick(); }}
            >
                Add Contributor
            </button>
        </h2>
        <s8f-records-table
            id="contributor-table"
            .tableColumns=${tableColumns}
            .tableRows=${contributors}
            .rowViewFunc=${(record) => { openLink(`/admin/super-eight-festivals/contributor/${record.id}/`); }}
            .rowEditFunc=${(record) => { btnEditClick(record); }}
            .rowDeleteFunc=${(record) => { btnDeleteClick(record); }}
        >
        </s8f-records-table>
    `;
}

customElements.define("s8f-contributors-table", component(ContributorsTable, { useShadowDOM: false }));
