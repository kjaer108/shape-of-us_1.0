/**
 * Builds a URL for the next step in the wizard, adapting for localhost or live environments.
 * Dynamically detects the language path from the current URL.
 * @param {number} nextStep - The next step number.
 * @returns {string} - The correct URL.
 */
function buildStepUrl(nextStep) {
    const currentPath = window.location.pathname;
    const isLocalhost = window.location.hostname === "localhost";

    let baseUrl;

    if (isLocalhost) {
        baseUrl = "http://localhost:63342/shape-of-us_1.0/dev/form.php";
    } else {
        const match = currentPath.match(/^\/([a-z]{2})\//);
        const langPath = match ? `/${match[1]}/` : "/";
        baseUrl = `https://shapeofus.eu${langPath}form/`;
    }

    return `${baseUrl}?step=${nextStep}`;
}

/**
 * Sends form data using URL-encoded format and redirects to the next step if successful.
 * @param {object} formData - The form data object.
 * @param {number} step - The current step number.
 * @param {number} nextStep - The next step number.
 */
function submitFormData(formData, step, nextStep) {
    formData.cmd = "save-step-state";
    const queryString = new URLSearchParams(formData).toString();

    fetch("src/xhr/form-save.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `step=${step}&${queryString}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = buildStepUrl(nextStep);
            } else {
                console.error("Error saving data:", data.error);
            }
        })
        .catch(error => {
            console.error("Request failed:", error);
        });
}

/** ---------------- Validation Functions for Each Step ---------------- **/

const stepHandlers = {

    1: function () {
        console.log("🚀 Step 1 activated.");

        // Category checkboxes
        const maleCheckbox = document.getElementById("category-male");
        const femaleCheckbox = document.getElementById("category-female");
        const intersexCheckbox = document.getElementById("category-intersex");
        const mtfCheckbox = document.getElementById("category-mtf");
        const ftmCheckbox = document.getElementById("category-ftm");

        // Skin tone checkboxes
        const skinCheckboxes = document.querySelectorAll("input[name='skin_tones']");

        // Submit button
        const submitButton = document.querySelector("button[type='submit']");

        /** ---------------- Ensure Only One Selection Between Two Checkboxes ---------------- **/
        function enforceSingleSelection(primary, secondary) {
            primary.addEventListener("change", function () {
                if (this.checked) {
                    secondary.checked = false;
                }
                validateStep1Form();
            });

            secondary.addEventListener("change", function () {
                if (this.checked) {
                    primary.checked = false;
                }
                validateStep1Form();
            });
        }

        /** ---------------- Ensure Only One Skin Tone Can Be Selected ---------------- **/
        skinCheckboxes.forEach(cb => {
            cb.addEventListener("change", function () {
                if (this.checked) {
                    skinCheckboxes.forEach(other => {
                        if (other !== this) other.checked = false;
                    });
                }
                validateStep1Form();
            });
        });

        /** ---------------- Step 1 Validation ---------------- **/
        function validateStep1Form() {
            let maleSelected = maleCheckbox.checked;
            let femaleSelected = femaleCheckbox.checked;
            let intersexSelected = intersexCheckbox.checked;
            let mtfSelected = mtfCheckbox.checked;
            let ftmSelected = ftmCheckbox.checked;

            // Ensure only Male OR Female is selected (not both)
            if (maleSelected && femaleSelected) {
                submitButton.disabled = true;
                return false;
            }

            // Ensure only MtF OR FtM is selected (not both)
            if (mtfSelected && ftmSelected) {
                submitButton.disabled = true;
                return false;
            }

            // At least one valid selection must be made
            if (!mtfSelected && !ftmSelected && !intersexSelected && !(maleSelected || femaleSelected)) {
                submitButton.disabled = true;
                return false;
            }

            submitButton.disabled = false;

            return {
                male: maleSelected ? "1" : "0",
                female: femaleSelected ? "1" : "0",
                intersex: intersexSelected ? "1" : "0",
                mtf: mtfSelected ? "1" : "0",
                ftm: ftmSelected ? "1" : "0"
            };
        }

        // Enforce single selection rules
        enforceSingleSelection(maleCheckbox, femaleCheckbox); // Male vs. Female
        enforceSingleSelection(mtfCheckbox, ftmCheckbox);     // MtF vs. FtM

        // Validate on any checkbox change
        [maleCheckbox, femaleCheckbox, intersexCheckbox, mtfCheckbox, ftmCheckbox].forEach(cb => {
            cb.addEventListener("change", validateStep1Form);
        });

        // Run validation on load
        validateStep1Form();

        return validateStep1Form;
    },

    2: function () {
        console.log("🚀 Step 2 activated.");

        const ageInput = document.getElementById("general-age");
        const skinRadios = document.querySelectorAll("input[name='skin_tones']");
        const residenceSelect = document.getElementById("general_residence");
        const birthSelect = document.getElementById("general_birth");
        const anatomyCheckboxes = document.querySelectorAll("input[id^='anatomy-']");
        const categoryInput = document.getElementById("category");

        const sectionMale = document.getElementById("section-male");
        const sectionFemale = document.getElementById("section-female");
        const sectionMtf = document.getElementById("section-mtf");
        const sectionFtm = document.getElementById("section-ftm");
        const submitButton = document.querySelector("button[type='submit']");

        /** ---------------- Preselection & Section Display ---------------- **/
        function updateAnatomySections() {
            if (!categoryInput) return;
            const categories = categoryInput.value.split(",").map(cat => cat.trim().toLowerCase());

            sectionMale.style.display = categories.includes("male") || categories.includes("intersex") ? "block" : "none";
            sectionFemale.style.display = categories.includes("female") || categories.includes("intersex") ? "block" : "none";
            sectionMtf.style.display = categories.includes("mtf") ? "block" : "none";
            sectionFtm.style.display = categories.includes("ftm") ? "block" : "none";
        }

        function preSelectAnatomy() {
            if (!categoryInput) return;
            const categories = categoryInput.value.split(",").map(cat => cat.trim().toLowerCase());

            if (categories.includes("female")) {
                document.getElementById("anatomy-female-vulva").checked = true;
            }
            if (categories.includes("male")) {
                document.getElementById("anatomy-male-penis").checked = true;
            }
        }

        /** ---------------- Step 2 Validation ---------------- **/
        function validateStep2Form() {
            let age = ageInput ? ageInput.value.trim() : "";
            let selectedSkinTone = document.querySelector("input[name='skin_tones']:checked");
            let residence = residenceSelect && residenceSelect.value ? residenceSelect.value.trim() : "";
            let birth = birthSelect && birthSelect.value ? birthSelect.value.trim() : "";
            let selectedAnatomy = Array.from(anatomyCheckboxes).filter(cb => cb.checked).map(cb => cb.id);

            console.log("📌 Age:", age);
            console.log("📌 Skin tone:", selectedSkinTone?.id);
            console.log("📌 Residence select value:", residence);
            console.log("📌 Birth select value:", birth);
            console.log("📌 Selected anatomy:", selectedAnatomy);

            let isAgeValid = age !== "" && parseInt(age) >= 18;
            let isSkinSelected = !!selectedSkinTone;
            let isResidenceSelected = residence !== "";
            let isBirthSelected = birth !== "";
            let isAnatomySelected = selectedAnatomy.length > 0;

            if (isAgeValid && isSkinSelected && isResidenceSelected && isBirthSelected && isAnatomySelected) {
                console.log("✅ Form valid - Enabling submit button.");
                submitButton.disabled = false;
                return {
                    age,
                    skin_tones: selectedSkinTone.id,
                    residence,
                    birth,
                    anatomy: selectedAnatomy.join(",")
                };
            } else {
                console.log("❌ Form invalid - Disabling submit button.");
                submitButton.disabled = true;
                return false;
            }
        }

        // Run functions on load
        updateAnatomySections();
        preSelectAnatomy();
        validateStep2Form();

        // Re-validate on input change
        ageInput?.addEventListener("input", validateStep2Form);
        skinRadios.forEach(radio => radio.addEventListener("change", validateStep2Form));
        residenceSelect?.addEventListener("change", () => {
            // If Country of Birth is not selected yet, mirror Country of Residence
            if (birthSelect && (!birthSelect.value || birthSelect.value.trim() === "")) {
                const newVal = residenceSelect ? residenceSelect.value : "";

                // Update underlying select value
                birthSelect.value = newVal;

                // If Choices.js enhanced select exists, update via its API for UI sync
                try {
                    const choicesInstance = window?.choices?.general_birth;
                    if (choicesInstance && typeof choicesInstance.setChoiceByValue === "function") {
                        choicesInstance.setChoiceByValue(newVal);
                    }
                } catch (e) {
                    console.debug("Choices update for #general_birth failed or not present:", e);
                }

                // Notify any listeners and our validation
                birthSelect.dispatchEvent(new Event("change", { bubbles: true }));
            }
            validateStep2Form();
        });
        birthSelect?.addEventListener("change", validateStep2Form);
        anatomyCheckboxes.forEach(cb => cb.addEventListener("change", validateStep2Form));

        return validateStep2Form;
    },

    3: function () {
        console.log("🚀 Step 3 activated.");

        // Get category input
        const categoryInput = document.getElementById("category");
        const categories = categoryInput ? categoryInput.value.split(",").map(cat => cat.trim().toLowerCase()) : [];

        // Section Elements (updated IDs)
        const sections = {
            vulva: document.getElementById("section-vulva"),
            penis: document.getElementById("section-penis"),
            trans: document.getElementById("section-trans"),
            buttocks: document.getElementById("section-buttocks"),
            hormone: document.getElementById("section-hormone"),
        };

        // ✅ Show/hide sections based on selected categories
        if (categories.length > 0) {
            sections.vulva.style.display = (categories.includes("female") || categories.includes("intersex")) ? "block" : "none";
            sections.penis.style.display = (categories.includes("male") || categories.includes("intersex")) ? "block" : "none";
            sections.trans.style.display = (categories.includes("mtf") || categories.includes("ftm")) ? "block" : "none";
            sections.buttocks.style.display = "block"; // always shown
            sections.hormone.style.display = "block";  // always shown
        }

        // "None to All" checkbox (excluded from backend submission)
        const noneToAllCheckbox = document.getElementById("medical-none-to-all");

        // Submit button
        const submitButton = document.querySelector("button[type='submit']");
        submitButton.disabled = true;

        const allNoneCheckboxes = document.querySelectorAll("input[id$='-none']:not(#trans-ftm-bottom-none)");
        const allOptionCheckboxes = document.querySelectorAll("input[type='checkbox']:not([id$='-none'])");

        function updateSubmitButtonState() {
            let allSectionsValid = true;

            Object.values(sections).forEach(section => {
                if (section && section.style.display !== "none") {
                    const checkboxes = section.querySelectorAll("input[type='checkbox']");
                    const hasChecked = Array.from(checkboxes).some(cb => cb.checked);
                    if (!hasChecked) allSectionsValid = false;
                }
            });

            submitButton.disabled = !allSectionsValid;
        }

        function handleNoneToAllSelection() {
            if (noneToAllCheckbox.checked) {
                console.log("✅ 'None to All' selected. Checking all 'None' checkboxes...");
                allNoneCheckboxes.forEach(cb => cb.checked = true);
                allOptionCheckboxes.forEach(cb => cb.checked = false);
                updateSubmitButtonState();
                submitButton.scrollIntoView({ behavior: "smooth" });
            }
        }

        function checkNoneToAllStatus() {
            const allNonesChecked = Array.from(allNoneCheckboxes).every(cb => cb.checked);
            noneToAllCheckbox.checked = allNonesChecked;
        }

        function handleOptionSelection() {
            const anyOptionSelected = Array.from(allOptionCheckboxes).some(cb => cb.checked);
            if (anyOptionSelected) {
                noneToAllCheckbox.checked = false;
            }
            checkNoneToAllStatus();
            updateSubmitButtonState();
        }

        function setupNoneDeselectLogic(section) {
            if (!section) return;

            const noneCheckbox = section.querySelector("input[id$='-none']");
            const otherCheckboxes = section.querySelectorAll("input[type='checkbox']:not([id$='-none'])");

            if (!noneCheckbox) return;

            noneCheckbox.addEventListener("change", function () {
                if (this.checked) {
                    otherCheckboxes.forEach(cb => cb.checked = false);
                }
                checkNoneToAllStatus();
                updateSubmitButtonState();
            });

            otherCheckboxes.forEach(cb => {
                cb.addEventListener("change", function () {
                    if (this.checked) {
                        noneCheckbox.checked = false;
                    }
                    checkNoneToAllStatus();
                    updateSubmitButtonState();
                });
            });
        }

        function collectStep3FormData() {
            let formData = {};

            // Collect all checked checkboxes except 'None to All'
            document.querySelectorAll("input[type='checkbox']:checked:not(#medical-none-to-all)").forEach(cb => {
                const id = cb.id;
                if (id.endsWith("-other-surgical-procedures")) return;
                formData[id] = "1";
            });

            // Add text inputs
            document.querySelectorAll("input[type='text']").forEach(input => {
                const val = input.value.trim();
                if (val !== "") formData[input.id] = val;
            });

            console.log("📤 Step 3 Form Data:", formData);
            return formData;
        }

        if (noneToAllCheckbox) {
            noneToAllCheckbox.addEventListener("change", handleNoneToAllSelection);
        }

        allOptionCheckboxes.forEach(cb => {
            cb.addEventListener("change", handleOptionSelection);
        });

        Object.values(sections).forEach(setupNoneDeselectLogic);

        updateSubmitButtonState();

        return function validateStep3Form() {
            console.log("Step 3 validation triggered.");
            return collectStep3FormData();
        };
    },

    4: function () {
        console.log("🚀 Step 4 activated.");

        const submitButton = document.querySelector("button[type='submit']");
        submitButton.disabled = false; // Step 4 is optional, always enable

        // Category-based visibility logic
        const categoryInput = document.getElementById("category");
        const categories = categoryInput ? categoryInput.value.split(",").map(cat => cat.trim().toLowerCase()) : [];
        const shouldShowPregnancySections = categories.includes("female") || categories.includes("ftm");

        const pregnancySection = document.getElementById("section-pregnancy");
        const vaginalBirthSection = document.getElementById("section-vaginal-birth");
        const cSectionSection = document.getElementById("section-c-section");
        const breastfeedingSection = document.getElementById("section-breastfeeding");
        const menstrualSection = document.getElementById("section-menstrual-cycle");

        [pregnancySection, vaginalBirthSection, cSectionSection, breastfeedingSection, menstrualSection].forEach(section => {
            if (section) {
                section.style.display = shouldShowPregnancySections ? "block" : "none";
            }
        });

        function getSelectedRadioValue(name) {
            const checked = document.querySelector(`input[name="${name}"]:checked`);
            return checked ? checked.id : null;
        }

        function getCheckedCheckboxes(selector) {
            return Array.from(document.querySelectorAll(selector))
                .filter(cb => cb.checked)
                .map(cb => cb.id);
        }

        function setupStretchMarksExclusivity() {
            const none = document.getElementById("marks-none");
            const stretch = document.getElementById("marks-stretch");
            const scars = document.getElementById("marks-scars");
            const scarsCollapse = document.getElementById("marks-scars-specify");

            if (!none || !stretch || !scars || !scarsCollapse) return;

            none.addEventListener("change", () => {
                if (none.checked) {
                    stretch.checked = false;
                    scars.checked = false;
                    const bsCollapse = bootstrap.Collapse.getInstance(scarsCollapse)
                        || new bootstrap.Collapse(scarsCollapse, { toggle: false });
                    bsCollapse.hide();
                    validateStep4Form();
                }
            });

            [stretch, scars].forEach(cb => {
                cb.addEventListener("change", () => {
                    if (cb.checked) {
                        none.checked = false;
                        if (cb === scars) {
                            const bsCollapse = bootstrap.Collapse.getInstance(scarsCollapse)
                                || new bootstrap.Collapse(scarsCollapse, { toggle: false });
                            bsCollapse.show();
                        }
                        validateStep4Form();
                    }

                    if (!scars.checked) {
                        const bsCollapse = bootstrap.Collapse.getInstance(scarsCollapse)
                            || new bootstrap.Collapse(scarsCollapse, { toggle: false });
                        bsCollapse.hide();
                    }
                });
            });
        }

        function setupMutualExclusionGroup(noCheckboxIdPrefix) {
            const noCheckbox = document.getElementById(`${noCheckboxIdPrefix}-false`);
            const otherCheckboxes = Array.from(document.querySelectorAll(`input[id^='${noCheckboxIdPrefix}-']:not(#${noCheckboxIdPrefix}-false)`));

            if (!noCheckbox) return;

            noCheckbox.addEventListener("change", () => {
                if (noCheckbox.checked) {
                    otherCheckboxes.forEach(cb => cb.checked = false);
                    validateStep4Form();
                }
            });

            otherCheckboxes.forEach(cb => {
                cb.addEventListener("change", () => {
                    if (cb.checked) {
                        noCheckbox.checked = false;
                        validateStep4Form();
                    }
                });
            });
        }

        function validateStep4Form() {
            const formData = {};

            const chest = getSelectedRadioValue("chest-hair");
            const genitalAbove = getSelectedRadioValue("genital-hair-above");
            const genital = getSelectedRadioValue("genital-hair");
            const buttocks = getSelectedRadioValue("buttocks-hair");

            const marks = getCheckedCheckboxes("input[id^='marks-']");
            const scarsText = document.getElementById("marks-scars-text")?.value.trim();

            const pregnancy = getSelectedRadioValue("pregnancy");
            const vaginalBirth = getSelectedRadioValue("vaginal-birth");
            const cSection = getSelectedRadioValue("c-section");
            const breastfeeding = getSelectedRadioValue("breastfeeding");

            const piercings = getCheckedCheckboxes("input[id^='piercings-']");
            const piercingsText = document.getElementById("piercings-other-text")?.value.trim();

            const tattoos = getCheckedCheckboxes("input[id^='tattoos-']");
            const tattoosText = document.getElementById("tattoos-other-text")?.value.trim();

            const hormonal = getSelectedRadioValue("hormonal-influence");
            const menstrual = getSelectedRadioValue("menstrual-cycle");

            // Only require visible sections
            const allValidConditions = [
                chest, genitalAbove, genital, buttocks,
                marks.length > 0 || scarsText,
                piercings.length > 0 || piercingsText,
                tattoos.length > 0 || tattoosText,
                hormonal
            ];

            if (pregnancySection?.style.display !== "none") {
                allValidConditions.push(pregnancy, vaginalBirth, cSection, breastfeeding);
            }

            if (menstrualSection?.style.display !== "none") {
                allValidConditions.push(menstrual);
            }

            const allValid = allValidConditions.every(Boolean);

            if (!allValid) {
                console.warn("⚠️ Step 4 has missing or partial data. Proceeding anyway (optional step).");
            }

            // Submit always enabled
            submitButton.disabled = false;

            // Data collection
            formData["chest_hair"] = chest;
            formData["genital_hair_above"] = genitalAbove;
            formData["genital_hair"] = genital;
            formData["buttocks_hair"] = buttocks;

            if (marks.length > 0) formData["marks"] = marks.join(",");
            if (scarsText) formData["marks-scars-text"] = scarsText;

            if (pregnancySection?.style.display !== "none") {
                formData["pregnancy"] = pregnancy;
                formData["vaginal_birth"] = vaginalBirth;
                formData["c_section"] = cSection;
                formData["breastfeeding"] = breastfeeding;
            }

            if (piercings.length > 0) formData["piercings"] = piercings.join(",");
            if (piercingsText) formData["piercings-other-text"] = piercingsText;

            if (tattoos.length > 0) formData["tattoos"] = tattoos.join(",");
            if (tattoosText) formData["tattoos-other-text"] = tattoosText;

            formData["hormonal_influence"] = hormonal;

            if (menstrualSection?.style.display !== "none") {
                formData["menstrual_cycle"] = menstrual;
            }

            console.log("📤 Step 4 Form Data (optional):", formData);
            return formData;
        }

        document.querySelectorAll("input").forEach(input => {
            input.addEventListener("change", validateStep4Form);
            input.addEventListener("input", validateStep4Form);
        });

        setupStretchMarksExclusivity();
        setupMutualExclusionGroup("piercings");
        setupMutualExclusionGroup("tattoos");

        validateStep4Form();

        return validateStep4Form;
    }


};

/** ---------------- Universal Form Handling ---------------- **/

document.addEventListener("DOMContentLoaded", function () {
    console.log("form.js loaded");

    const form = document.querySelector("form");
    const currentStep = parseInt(form.getAttribute("data-step")) || 1;
    console.log("Current step:", currentStep);

    if (stepHandlers[currentStep]) {
        const validateForm = stepHandlers[currentStep]();

        form.addEventListener("submit", function (event) {
            event.preventDefault();
            const nextStep = currentStep + 1;

            let formData = validateForm();
            if (formData) submitFormData(formData, currentStep, nextStep);
        });
    }
});