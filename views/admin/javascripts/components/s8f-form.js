import { html, nothing } from "../../../shared/javascripts/vendor/lit-html.js";
import { component, useEffect, useState } from "../../../shared/javascripts/vendor/haunted.js";
import { repeat } from "../../../shared/javascripts/vendor/lit-html/directives/repeat.js";

import { eventBus, S8FEvent } from "../../../shared/javascripts/event-bus.js";
import Alerts from "../utils/alerts.js";

const S8FForm = (element) => {
    const [submitting, setSubmitting] = useState(false);

    useEffect(() => {
        const formSubmitRequestSub = eventBus.subscribe(S8FEvent.RequestFormSubmit, () => {
            setSubmitting(true);
        });
        const formSubmitCompleteSub = eventBus.subscribe(S8FEvent.CompleteFormSubmit, () => {
            setSubmitting(false);
        });

        return () => {
            formSubmitRequestSub.unsubscribe();
            formSubmitCompleteSub.unsubscribe();
        };
    }, []);

    const triggerCancelEvent = () => {
        if (submitting) return;

        element.dispatchEvent(new CustomEvent("cancel", {
            detail: new FormData(element.querySelector("form")),
        }));
        element.querySelector("form").reset();
    };

    const triggerSubmitEvent = () => {
        if (submitting) return;

        const formData = new FormData(element.querySelector("form"));

        let validationResult;
        if (element.validateFunc) {
            validationResult = element.validateFunc(formData);
            if (validationResult) {
                Alerts.error("form-alerts", "Invalid Form Data", validationResult.message, true);
                return;
            }
        }

        element.dispatchEvent(new CustomEvent("submit", {
            detail: formData,
        }));
    };

    const formElementTemplate = (elem) => {
        const elemID = `form-elem-${elem.name}`;
        const labelElem = elem.label ? html`<label for=${elemID} class="form-label">${elem.label}</label>` : nothing;

        let inputElem;
        switch (elem.type) {
            default:
            case "text":
                inputElem = html`
                    <input
                        type="text"
                        class=${`form-control`}
                        id=${elemID}
                        name=${elem.name}
                        placeholder=${elem.placeholder ? elem.placeholder : ""}
                        .value=${elem.value}
                    >
                `;
                break;
            case "number":
                inputElem = html`
                    <input
                        type="number"
                        class="form-control"
                        step="0.0001"
                        id=${elemID}
                        name=${elem.name}
                        placeholder=${elem.placeholder ? elem.placeholder : ""}
                        .value=${elem.value}
                    >
                `;
                break;
            case "textarea":
                inputElem = html`
                    <textarea
                        class="form-control"
                        id=${elemID}
                        name=${elem.name}
                        placeholder=${elem.placeholder ? elem.placeholder : ""}
                        .value=${elem.value}
                    >
                `;
                break;
            case "file":
                inputElem = html`
                    <input
                        type="file"
                        class="form-control"
                        id=${elemID}
                        name=${elem.name}
                        accept=${elem.accept}
                    >
                `;
                break;
            case "select":
                inputElem = html`
                    <select
                        class="form-select"
                        id=${elemID}
                        name=${elem.name}
                        .value=${elem.value}
                    >
                        ${repeat(elem.options, (opt) => html`
                            <option value=${opt.value} .selected=${opt.selected}>${opt.label}</option>
                        `)}
                    </select>
                `;
                break;
            case "description":
                inputElem = html`<p class=${elem.styleClasses ? elem.styleClasses.join(" ") : ""}>${elem.value}</p>`;
                break;
            case "toggle":
                inputElem = html`
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id=${elemID} name=${elem.name} .checked=${elem.value}>
                        <label class="form-check-label" for=${elemID}>
                            ${elem.label}
                        </label>
                    </div>
                `;
                break;
        }

        return html`
            <div class="mb-3 ${"visible" in elem && !elem.visible ? "d-none" : ""}">
                ${elem.type !== "toggle" ? (
                    labelElem
                ) : null}
                ${inputElem}
            </div>
        `;
    };

    return html`
        <s8f-alerts-area id="form-alerts"></s8f-alerts-area>
        <form id=${element.formId}>
            <fieldset ?disabled=${submitting}>
                ${repeat(element.elements, e => e, elem => formElementTemplate(elem))}
                <div class="mb-3 float-right">
                    <button type="button" class="btn btn-secondary" @click=${triggerCancelEvent}>Cancel</button>
                    <button type="button" class="btn btn-primary" @click=${triggerSubmitEvent}>Submit</button>
                </div>
            </fieldset>
        </form>
    `;
};

S8FForm.observedAttributes = ["form-id"];

customElements.define("s8f-form", component(S8FForm, { useShadowDOM: false }));
