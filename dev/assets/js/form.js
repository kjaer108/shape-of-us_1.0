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
                //window.location.href = buildStepUrl(nextStep);
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

        // Enforce single selection for:
        enforceSingleSelection(maleCheckbox, femaleCheckbox); // Male vs. Female
        enforceSingleSelection(mtfCheckbox, ftmCheckbox); // MtF vs. FtM

        // Validate form on any checkbox change
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
        const skinCheckboxes = document.querySelectorAll("input[id^='skin-']");
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
            let selectedSkinTones = Array.from(skinCheckboxes).filter(cb => cb.checked).map(cb => cb.id);
            let residence = residenceSelect && residenceSelect.value ? residenceSelect.value.trim() : "";
            let birth = birthSelect && birthSelect.value ? birthSelect.value.trim() : "";
            let selectedAnatomy = Array.from(anatomyCheckboxes).filter(cb => cb.checked).map(cb => cb.id);

            console.log("📌 Age:", age);
            console.log("📌 Skin tones:", selectedSkinTones);
            console.log("📌 Residence select value:", residence);
            console.log("📌 Birth select value:", birth);
            console.log("📌 Selected anatomy:", selectedAnatomy);

            let isAgeValid = age !== "" && parseInt(age) >= 18;
            let isSkinSelected = selectedSkinTones.length > 0;
            let isResidenceSelected = residence !== "";
            let isBirthSelected = birth !== "";
            let isAnatomySelected = selectedAnatomy.length > 0;

            if (isAgeValid && isSkinSelected && isResidenceSelected && isBirthSelected && isAnatomySelected) {
                console.log("✅ Form valid - Enabling submit button.");
                submitButton.disabled = false;
                return {
                    age,
                    skin_tones: selectedSkinTones.join(","),
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

        return validateStep2Form;
    },

    3: function () {
        console.log("🚀 Step 3 activated.");

        // Get category input
        const categoryInput = document.getElementById("category");
        const categories = categoryInput ? categoryInput.value.split(",").map(cat => cat.trim().toLowerCase()) : [];

        // Section Elements
        const sections = {
            vulvaVulva: document.getElementById("section-vulva-vulva"),
            vulvaBreast: document.getElementById("section-vulva-breast"),
            penisPenis: document.getElementById("section-penis-penis"),
            penisBreast: document.getElementById("section-penis-breast"),
            transMtF: document.getElementById("section-trans-mtf"),
            transFtM: document.getElementById("section-trans-ftm"),
            buttocks: document.getElementById("section-buttocks"),
            hormone: document.getElementById("section-hormone"),
        };

        // "None to All" checkbox (excluded from backend submission)
        const noneToAllCheckbox = document.getElementById("medical-none-to-all");

        // Submit button
        const submitButton = document.querySelector("button[type='submit']");
        submitButton.disabled = true; // Initially disabled

        // Get all "None" checkboxes (excluding trans-ftm-bottom-none)
        const allNoneCheckboxes = document.querySelectorAll("input[id$='-none']:not(#trans-ftm-bottom-none)");

        // Get all option checkboxes (excluding None checkboxes)
        const allOptionCheckboxes = document.querySelectorAll("input[type='checkbox']:not([id$='-none'])");

        /** ---------------- Enable Submit Button if Every Visible Section Has a Selection ---------------- **/
        function updateSubmitButtonState() {
            let allSectionsValid = true;

            Object.values(sections).forEach(section => {
                if (section && section.style.display !== "none") {
                    const checkboxes = section.querySelectorAll("input[type='checkbox']");
                    const hasChecked = Array.from(checkboxes).some(cb => cb.checked);

                    if (!hasChecked) {
                        allSectionsValid = false;
                    }
                }
            });

            submitButton.disabled = !allSectionsValid;
        }

        /** ---------------- Handle 'None to All' Selection ---------------- **/
        function handleNoneToAllSelection() {
            if (noneToAllCheckbox.checked) {
                console.log("✅ 'None to All' selected. Checking all 'None' checkboxes...");

                // Select all "None" checkboxes
                allNoneCheckboxes.forEach(cb => cb.checked = true);

                // Deselect all other checkboxes
                allOptionCheckboxes.forEach(cb => cb.checked = false);

                // Ensure submit button validation updates
                updateSubmitButtonState();

                // Scroll to submit button
                submitButton.scrollIntoView({ behavior: "smooth" });
            }
        }

        /** ---------------- Ensure 'None to All' remains checked if all 'None' checkboxes are selected ---------------- **/
        function checkNoneToAllStatus() {
            const allNonesChecked = Array.from(allNoneCheckboxes).every(cb => cb.checked);
            noneToAllCheckbox.checked = allNonesChecked;
        }

        /** ---------------- Deselect 'None to All' if any option is selected ---------------- **/
        function handleOptionSelection() {
            const anyOptionSelected = Array.from(allOptionCheckboxes).some(cb => cb.checked);

            if (anyOptionSelected) {
                noneToAllCheckbox.checked = false; // Deselect "None to all"
            }

            checkNoneToAllStatus();
            updateSubmitButtonState();
        }

        /** ---------------- Apply Deselect Logic to Each Section ---------------- **/
        function setupNoneDeselectLogic(section) {
            if (!section) return;

            const noneCheckbox = section.querySelector("input[id$='-none']");
            const otherCheckboxes = section.querySelectorAll("input[type='checkbox']:not([id$='-none'])");

            if (!noneCheckbox) return;

            // If "None" is selected, deselect all other options in that section
            noneCheckbox.addEventListener("change", function () {
                if (this.checked) {
                    otherCheckboxes.forEach(cb => cb.checked = false);
                }
                checkNoneToAllStatus();
                updateSubmitButtonState();
            });

            // If any other checkbox is selected, deselect "None"
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
            let sectionGroups = {};

            // Define sections that should be grouped together
            const groupedSections = {
                "section-penis-breast": ["medical-penis-breast"],
                "section-penis-penis": ["medical-penis"],
                "section-vulva-breast": ["medical-breast"],
                "section-vulva-vulva": ["medical-vulva"],
                "section-trans-ftm": ["trans-ftm"],
                "section-trans-mtf": ["trans-mtf"],
                "section-buttocks": ["buttocks"],
                "section-hormone": ["hormone"]
            };

            // Sort section keys so longest prefixes come first
            const sortedGroupKeys = Object.keys(groupedSections).sort((a, b) => {
                const aLen = groupedSections[a][0][0].length;
                const bLen = groupedSections[b][0][0].length;
                return bLen - aLen;
            });

            // Collect all checked checkboxes except 'None to All' and skip checkboxes ending with '-other-surgical-procedures'
            document.querySelectorAll("input[type='checkbox']:checked:not(#medical-none-to-all)").forEach(cb => {
                const checkboxId = cb.id;

                // Don't include -other-surgical-procedures checkboxes in output
                if (checkboxId.endsWith("-other-surgical-procedures")) return;

                let matchedGroupKey = sortedGroupKeys.find(key =>
                    groupedSections[key].some(prefix => checkboxId.startsWith(prefix))
                );

                if (matchedGroupKey) {
                    if (!sectionGroups[matchedGroupKey]) {
                        sectionGroups[matchedGroupKey] = [];
                    }
                    sectionGroups[matchedGroupKey].push(checkboxId);
                } else {
                    // If not matched to any group, add individually
                    formData[checkboxId] = "1";
                }
            });

            // Convert grouped sections to comma-separated values
            Object.entries(sectionGroups).forEach(([sectionId, ids]) => {
                formData[sectionId] = ids.join(",");
            });

            // Collect all text inputs (skip empty)
            document.querySelectorAll("input[type='text']").forEach(input => {
                const val = input.value.trim();
                if (val !== "") {
                    formData[input.id] = val;
                }
            });

            console.log("📤 Step 3 Form Data:", formData);
            return formData;
        }




        /** ---------------- Attach Event Listeners ---------------- **/
        if (noneToAllCheckbox) {
            noneToAllCheckbox.addEventListener("change", handleNoneToAllSelection);
        }

        allOptionCheckboxes.forEach(cb => {
            cb.addEventListener("change", handleOptionSelection);
        });

        // Apply logic for each independent section
        Object.values(sections).forEach(setupNoneDeselectLogic);

        // Initial state check
        updateSubmitButtonState();

        return function validateStep3Form() {
            console.log("Step 3 validation triggered.");
            return collectStep3FormData();
        };
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
