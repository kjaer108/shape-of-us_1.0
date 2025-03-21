/* eslint-disable no-unused-vars */
/* eslint-disable no-undef */
;(function (document, window) {
  'use strict';

  const filtersForm = document.getElementById('offcanvas-filters');
  const apiUrl = 'http://localhost:8001/get_images.php';
  const viewer = OpenSeadragon({
    id: "app",
    prefixUrl: "https://openseadragon.github.io/openseadragon/images/",
    immediateRender: true,
    showNavigator: true,
    collectionMode: false,
    visibilityRatio: 1,
    minZoomLevel: 0.5,
    maxZoomLevel: 5,
    defaultZoomLevel: 1,
  });

  let loadedPositions = new Map(); // Map<row, Set<column>>
  let imageSize = 0.1; // Normalized size for OpenSeadragon (100px in grid)
  let padding = 0.005; // Small gap between images
  let isLoading = false; // Flag to prevent concurrent loading operations
  let currentFilters = {}; // Store current filter settings
  let wasDragging = false;
  let batchSize = 500;
  let totalImages = 5000;

  console.log('window', window.screen.width, window.screen.height);
  console.log('osViewer', viewer);
  console.log('osViewer.viewport', viewer.viewport);
  console.log('osViewer.viewport.getBounds()', viewer.viewport.getBounds());
  console.log('osViewer.viewport.getCenter()', viewer.viewport.getCenter());
  console.log('osViewer.viewport.getContainerSize()', viewer.viewport.getContainerSize());

  // setTimeout(() => {
  //   console.log('panning to center');
  //   console.log('viewport.center', viewer.viewport.getCenter());
  //   console.log('OpenSeadragon.Point', new OpenSeadragon.Point(0.5, 0.5));
  //   // Pan to the center of canvas
  //   viewer.viewport.panTo(new OpenSeadragon.Point(0.75, 0.95));
  // }, 1000);

  initialize(viewer, filtersForm);

  /**
   * Initialize the grid and set up event handlers
   * @param {OpenSeadragon.Viewer} viewer - The OpenSeadragon viewer instance
   * @param {HTMLFormElement} filtersForm - The form element containing filters
   */
  function initialize(viewer, filtersForm) {
    // Parse URL parameters for initial filters
    const urlParams = parseUrlParameters();
    currentFilters = urlParams;
    
    // Populate form with URL parameters
    if (filtersForm) {
      populateFIlterFormFromUrl(filtersForm, urlParams);
      
      // Set up form submission handler
      filtersForm.addEventListener('submit', (event) => {
        handleFilterFormSubmit(event, viewer, filtersForm);
      });

      filtersForm.addEventListener('reset', (event) => {
        handleFilterFormReset(event, viewer, filtersForm);
      });

      // On and changes in controls need to update the offcanvas trigger button
      filtersForm.addEventListener('change', handleOffcanvasToggleUpdate);

      // Sync form controls in "filtersForm .nav-tabs" with controls in "header"
      filtersForm.querySelectorAll('.nav-tabs input, .nav-tabs select').forEach(control => {
        control.addEventListener('change', handleChangeFilterCategories);
      });

      // Sync form controls in "header" with controls in "filtersForm .nav-tabs"
      document.querySelectorAll('header .nav input[type="checkbox"]').forEach(control => {
        // Pass filterForm as a second argument to the handler
        control.addEventListener('change', handleChangeHeaderCategories.bind(null, filtersForm));
      });

      // Listen for changes on form controls inside the .accordion
      filtersForm.querySelectorAll('.accordion-body input, .accordion-body select').forEach(control => {
        control.addEventListener('change', handleChangeFilterGroups);
      });

      // Global event handler for reset buttons
      document.addEventListener('click', function(event) {
        // Check if clicked element is a reset button
        if (
          event.target.hasAttribute('data-sou-reset-filter-group') || 
          event.target.closest('[data-sou-reset-filter-group]')
        ) {
          console.log(['document.click.data-sou-reset-filter-group', event]);
          handleFilterGroupResetClick(event);
        }
      });
    }
    
    // Initial load based on viewport
    loadVisibleImages(viewer, currentFilters);
    
    // Load more images when viewport changes
    viewer.addHandler('animation-finish', function() {
      loadVisibleImages(viewer, currentFilters);
    });
    
    // Add handler for zoom change
    viewer.addHandler('zoom', function(event) {
      // todo: this about throttling this to avoid too many calculations
      loadVisibleImages(viewer, currentFilters);
    });
    
    // Handle browser back/forward navigation
    window.addEventListener('popstate', function(event) {
      console.log('popstate', event);
      if (event.state && event.state.filters) {
        // Update current filters from history state
        currentFilters = event.state.filters;
        
        // Update form fields
        if (filtersForm) {
          populateFIlterFormFromUrl(filtersForm, currentFilters);
        }
        
        // Reload images with new filters
        clearAllImages(viewer);
        loadVisibleImages(viewer, currentFilters);
      }
    });
  }

  /**
   * Parse URL parameters and return them as an object
   * @returns {Object} - URL parameters as key-value pairs
   */
  function parseUrlParameters() {
    const searchParams = new URLSearchParams(window.location.search);
    const params = {};
    
    for (const [key, value] of searchParams.entries()) {
      params[key] = value;
    }
    
    return params;
  }

  /**
   * Calculate positions that need to be loaded based on viewport
   * @param {OpenSeadragon.Viewer} viewer - The OpenSeadragon viewer instance
   * @returns {Object} - Contains positions array and grid dimensions
   */
  function calculatePositionsToLoad(viewer) {
    // Get current viewport dimensions in viewport coordinates
    const viewportBounds = viewer.viewport.getBounds();
    const viewportWidth = viewportBounds.width;
    const viewportHeight = viewportBounds.height;
    
    // Calculate how many images can fit in the viewport
    const itemWidth = imageSize + padding;
    const itemHeight = imageSize + padding;
    
    // Calculate number of columns and rows that fit in the viewport
    // Adding 1 to ensure we cover the edges
    const numCols = Math.ceil(viewportWidth / itemWidth) + 1;
    const numRows = Math.ceil(viewportHeight / itemHeight) + 1;
    
    // Calculate starting position (top-left corner of viewport)
    const startX = Math.floor(viewportBounds.x / itemWidth);
    const startY = Math.floor(viewportBounds.y / itemHeight);
    
    // Create array of positions that need to be loaded
    const positions = [];
    for (let row = startY; row < startY + numRows; row++) {
      // Initialize Set for this row if it doesn't exist
      if (!loadedPositions.has(row)) {
        loadedPositions.set(row, new Set());
      }
      
      for (let col = startX; col < startX + numCols; col++) {
        // Check if this position is already loaded
        if (loadedPositions.get(row).has(col)) continue;
        
        // Mark position as loaded
        loadedPositions.get(row).add(col);
        positions.push({ col, row });
      }
    }
    
    return {
      positions,
      numCols,
      numRows,
      startX,
      startY
    };
  }

  /**
   * Fetch images from the server using POST
   * @param {number} limit - Number of images to fetch
   * @param {Object} filters - Filter criteria to apply
   * @returns {Promise} - Promise resolving to image URLs
   */
  async function fetchImages(limit, filters = {}) {
    console.log(['fetchImages', limit, filters]);
    // Create request body including limit and all filters
    // Send POST request as form data
    const requestBody = {
      limit,
      ...filters
    };

    const formData = new FormData();
    Object.entries(requestBody).forEach(([key, value]) => {
      if (Array.isArray(value)) {
        value.forEach(v => formData.append(`${key}[]`, v));
      } else {
        formData.append(key, value);
      }
    });

    const fetchOptions = {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: formData
    };
    
    // Send POST request with JSON body
    const response = await fetch(apiUrl, fetchOptions);
    if (!response.ok) {
      throw new Error(`Network response was not ok: ${response.status}`);
    }
    return await response.json();
  }

  /**
   * Render images in the viewer
   * @param {OpenSeadragon.Viewer} viewer - The OpenSeadragon viewer
   * @param {Array} imageUrls - Array of image URLs to render
   * @param {Array} positions - Array of position objects {col, row}
   */
  function renderImages(viewer, imageUrls, positions) {
    imageUrls.forEach((url, i) => {
      if (i >= positions.length) return; // Guard against mismatched arrays
      
      let { col, row } = positions[i];
      
      viewer.addSimpleImage({
        url: url,
        x: col * (imageSize + padding),
        y: row * (imageSize + padding),
        width: imageSize,
        success: function(event) {
          // todo: should we do anything here?
        },
        error: function(event) {
          // Handle failed image loads
          console.error('Failed to load image:', url);
          // Remove from the Map of Sets structure
          if (loadedPositions.has(row)) {
            loadedPositions.get(row).delete(col);
            // Optionally remove the row Set if it's empty
            if (loadedPositions.get(row).size === 0) {
              loadedPositions.delete(row);
            }
          }
        }
      });
    });
  }

  /**
   * Load visible images based on current viewport
   * @param {OpenSeadragon.Viewer} viewer - The OpenSeadragon viewer
   * @param {Object} filters - Filter criteria to apply
   */
  function loadVisibleImages(viewer, filters = {}) {
    if (isLoading) return; // Prevent concurrent loading
    isLoading = true;
    
    // Calculate which positions need to be loaded
    const { positions, numCols, numRows } = calculatePositionsToLoad(viewer);
    
    if (positions.length === 0) {
      isLoading = false;
      return;
    }
    
    // Log the batch size for debugging
    console.log(`Loading batch: ${positions.length} images (${numCols}x${numRows} grid)`);
    
    // Fetch and render images
    fetchImages(positions.length, filters)
      .then(imageUrls => {
        renderImages(viewer, imageUrls, positions);
        isLoading = false;
      })
      .catch(error => {
        console.error('Error loading images:', error);
        // Free up positions that weren't loaded successfully
        positions.forEach(({ col, row }) => {
          // Remove from the Map of Sets structure
          if (loadedPositions.has(row)) {
            loadedPositions.get(row).delete(col);
            // Optionally remove the row Set if it's empty
            if (loadedPositions.get(row).size === 0) {
              loadedPositions.delete(row);
            }
          }
        });
        isLoading = false;
      });
  }

  /**
   * Clear all loaded images from the viewer and reset state
   * @param {OpenSeadragon.Viewer} viewer - The OpenSeadragon viewer
   */
  function clearAllImages(viewer) {
    // Remove all images from the viewer
    viewer.world.removeAll();
    
    // Reset tracking of loaded positions
    loadedPositions.clear();
  }

  /**
   * Update URL with current filters
   * @param {Object} filters - Current filter settings
   */
  function updateUrlWithFilters(filters) {
    // Create URL parameters from filters.
    // Skip null, undefined, and empty values.
    // Take into account, that we have checkboxes with the same name,
    // e.g. `category[]` and in `filters` object they are stored as arrays.
    // In URL it will be comma-separated values, e.g. `category=a,b,c`
    const params = new URLSearchParams();
    Object.entries(filters).forEach(([key, value]) => {
      if (value === null || value === undefined) return;
      
      if (Array.isArray(value)) {
        params.append(key, value.join(','));
      } else {
        params.append(key, value);
      }
    });

    console.log('updateUrlWithFilters.params', params);
    
    // Update URL without reloading the page
    const newUrl = `${window.location.pathname}?${params.toString()}`;
    window.history.pushState({ filters }, '', newUrl);
  }

  /**
   * Populate form fields based on URL parameters
   * @param {HTMLFormElement} form - The filter form element
   * @param {Object} params - URL parameters
   */
  function populateFIlterFormFromUrl(form, params) {
    console.log('populateFormFromUrl', form, params);
    Object.entries(params).forEach(([key, value]) => {
      const element = form.elements[key];
      console.log(['populateFormFromUrl.element', element, key, value]);
      if (!element) return;
      
      // Handle different input types
      if (element.type === 'checkbox') {
        element.checked = value === 'true' || value === '1';
      } else if (element.type === 'radio') {
        const radio = form.querySelector(`input[name="${key}"][value="${value}"]`);
        if (radio) radio.checked = true;
      } else if (element.tagName === 'SELECT') {
        element.value = value;
      } else {
        element.value = value;
      }
    });
  }

  /**
   * Handle form submission and reload images with filters
   * @param {Event} event - Form submit event
   * @param {OpenSeadragon.Viewer} viewer - The OpenSeadragon viewer
   * @param {HTMLFormElement} form - The filter form element
   */
  function handleFilterFormSubmit(event, viewer, form) {
    event.preventDefault();
    
    // Extract form data into filters object
    const formData = new FormData(form);
    const filters = {};
    
    // Convert FormData to object.
    // Need to take into account that our form has checkboxes with the same name,
    // e.g. 'category[]', so we nned to handle them.
    for (let [key, value] of formData.entries()) {
      // Skip empty values
      if (!value) continue;

      if (key.endsWith('[]')) {
        key = key.slice(0, -2);
        if (!filters[key]) {
          filters[key] = [];
        }
        filters[key].push(value);
      } else {
        filters[key] = value;
      }
    }
    
    // Update current filters
    currentFilters = filters;

    console.log('handleFilterFormSubmit.filters', filters);
    
    // Update URL with new filters
    updateUrlWithFilters(filters);
    
    // Clear all existing images
    clearAllImages(viewer);
    
    // Reset viewer to initial position if needed
    viewer.viewport.goHome();
    
    // Load new images with filters
    loadVisibleImages(viewer, filters);
  }

  /**
   * Reset the filters form to its initial state.
   * @param {Event} event - Form submit event
   * @param {OpenSeadragon.Viewer} viewer - The OpenSeadragon viewer
   * @param {HTMLFormElement} form - The filter form element
   */
  function handleFilterFormReset(event, viewer, form) {
    console.log('handleFilterFormReset', event);

    event.preventDefault();
    event.stopPropagation();

    // Reset form controls
    const controls = form.querySelectorAll('input[type="checkbox"], input[type="radio"], select');
    resetFilterControls(controls);
    
    // Reset current filters
    updateUrlWithFilters({});

    // Clear all existing images
    clearAllImages(viewer);

    // Reset viewer to initial position
    viewer.viewport.goHome();

    // Load new images without filters
    loadVisibleImages(viewer, {});
  }

  /**
   * Handle the click on the reset button for a filter group.
   * @param {Event} event - Form submit event
   */
  function handleFilterGroupResetClick(event) {
    event.preventDefault();
    event.stopPropagation();
    
    const resetButton = event.target.hasAttribute('data-sou-reset-filter-group') ? 
                        event.target : 
                        event.target.closest('[data-sou-reset-filter-group]');
    
    const accordionItemId = resetButton.getAttribute('data-sou-reset-filter-group').slice(1);
    const accordionItem = document.getElementById(accordionItemId);
    const accordionHeader = accordionItem?.querySelector('.accordion-header');
    const accordionBody = accordionItem?.querySelector('.accordion-body');

    if (!accordionItem || !accordionHeader || !accordionBody) {
      // Looks like a broken markup, let's bail out
      console.warn('Control is not inside an .accordion-item', resetButton);
      return;
    }

    // Reset all form controls in this section
    const controls = accordionBody.querySelectorAll('input[type="checkbox"], input[type="radio"], select');
    
    resetFilterControls(controls, resetButton);

    // Remove the reset button
    resetButton.remove();
  }

  /**
   * Handle changes to form controls (checkboxes, selects, etc.)
   * @param {Event} event - Change event on a form control
   */
  function handleChangeFilterGroups(event) {
    console.log(['handleControlChangeInFilterGroups', event]);
    const control = event.target;
    const accordionItem = control.closest('.accordion-item');
    const accordionHeader = accordionItem?.querySelector('.accordion-header');
    const accordionBody = accordionItem?.querySelector('.accordion-body');
    if (!accordionItem || !accordionHeader || !accordionBody) {
      // Looks like a broken markup, let's bail out
      console.warn('Control is not inside an .accordion-item', control);
      return;
    }
    
    const accordionItemId = accordionItem.id;

    // Find the child element of accordionItem with selector [data-sou-selected-filter-area="#{accordionItemId}"]
    // Note 1: element may be missing. In this case we need to create it.
    // Note 2: element should be appended to .accrodion-header
    let selectedFiltersArea = accordionItem.querySelector(`[data-sou-selected-filter-area="#${accordionItemId}"]`);
    if (!selectedFiltersArea) {
      // Create the element if it doesn't exist
      selectedFiltersArea = createSelectedFilterArea(accordionItemId);
      accordionHeader.appendChild(selectedFiltersArea);
    }
    
    updateFilterCount(selectedFiltersArea, accordionBody);
  }

  function handleOffcanvasToggleUpdate(event) {
    console.log(['handleOffcanvasToggleUpdate', event]);
    const offcanvasToggle = document.querySelector('header [data-bs-toggle="offcanvas"]');
    if (!offcanvasToggle) {
      // Looks like a broken markup, let's bail out
      console.warn('Offcanvas toggle button not found');
      return;
    }

    // Check if any form control is checked
    const controls = event.currentTarget.querySelectorAll('input[type="checkbox"], input[type="radio"], select');
    const affectedControlsCount = countAffectedControls(controls);
    console.log('affectedControlsCount', affectedControlsCount);

    // Update the text of the offcanvas trigger button based on filter count
    // Filtets (n) where n is the number of selected filters
    // If no filters are selected, just show "Filters"
    const buttonText = affectedControlsCount > 0 ? `Filters (${affectedControlsCount})` : 'Filters';
    offcanvasToggle.textContent = buttonText
  }

  /**
   * Handle changes to form controls in the filter .nav-tabs.
   * @param {Event} event - Change event on a form control.
   */
  function handleChangeFilterCategories(event) {
    console.log(['handleChangeFilterCategories', event]);
    const control = event.target;
    const navTab = control.closest('.nav-tabs');
    if (!navTab) {
      // Looks like a broken markup, let's bail out
      console.warn('Control is not inside a .nav-tabs', control);
      return;
    }

    // Sync with exact same control in the header navigation
    // (same name, same value)
    const controlName = control.name;
    const controlValue = control.value;
    const headerControls = document.querySelectorAll(`header [name="${controlName}"]`);
    headerControls.forEach(headerControl => {
      if (headerControl.value === controlValue) {
        headerControl.checked = control.checked;
      }
    });
  }

  /**
   * Handle changes to form controls in the header navigation.
   * 
   * On change of a control in the header navigation we need to sync the same control
   * in the filter .nav-tabs.
   * 
   * @param {HTMLFormElement} filtersForm - The filter form element.
   * @param {Event} event - Change event on a form control.
   */
  function handleChangeHeaderCategories(filtersForm, event) {
    console.log(['handleChangeHeaderCategories', filtersForm, event]);
    const control = event.target;
    const navItem = control.closest('.nav-item');
    if (!navItem) {
      // Looks like a broken markup, let's bail out
      console.warn('Control is not inside a .nav-item', control);
      return;
    }

    // Sync with exact same control in the header navigation
    // (same name, same value)
    const controlName = control.name;
    const controlValue = control.value;
    const navControls = filtersForm.querySelectorAll(`.nav-tabs [name="${controlName}"]`);
    navControls.forEach(navControl => {
      if (navControl.value === controlValue) {
        navControl.checked = control.checked;
      }
    });

    // Dispatch change event on form to update offcanvas trigger button
    filtersForm.dispatchEvent(new Event('change', { bubbles: true }));

    // Dispatch form submit event to update images
    filtersForm.dispatchEvent(new Event('submit', { bubbles: true }));
  }

  /**
   * Create a selected filter area element.
   * @param {string} targetId 
   * @returns 
   */
  function createSelectedFilterArea(targetId) {
    let element = document.createElement('span');
    element.className = 'd-flex gap-1 flex-shrink-0 me-4 pe-2 position-absolute end-0 top-50 translate-middle-y z-3';
    element.setAttribute('data-sou-selected-filter-area', `#${targetId}`);
    return element;
  }

  /**
   * Create a reset filter group button element.
   * @param {string} targetId 
   * @param {string} buttonText
   * @returns 
   */
  function createResetFilterGroupButton(targetId, buttonText = 'Clear filters') {
    let button = document.createElement('button');
    button.type = 'button';
    button.className = 'btn btn-sm btn-filter rounded-pill';
    button.setAttribute('data-sou-reset-filter-group', `#${targetId}`);

    let icon = document.createElement('i');
    icon.className = 'btn-filter-badge zi-close';
    button.appendChild(icon);

    let span = document.createElement('span');
    span.textContent = buttonText;
    button.appendChild(span);

    return button;
  }

  /**
   * Update the filter count for a section based on selected controls
   */
  function updateFilterCount(selectedFilterArea, accordionBody) {
    // Get all form controls in this accordion body, grouped by name
    const controls = accordionBody.querySelectorAll('input, select');
    let affectedControlsCount = countAffectedControls(controls);
    console.log(['updateFilterCount.affectedControlsCount', affectedControlsCount]);
    
    // Update or remove the reset button based on filter count
    const targetId = selectedFilterArea.getAttribute('data-sou-selected-filter-area').slice(1);
    const buttonText = `${affectedControlsCount} filter${affectedControlsCount > 1 ? 's' : ''}`;
    let resetButton = selectedFilterArea.querySelector('[data-sou-reset-filter-group]');
    // If there are no selected filters and reset button is missing, just skip.
    // If there are no selected filters and reset button is present, remove it.
    // If there are selected filters and reset button is missing, create it.
    // If there are selected filters and reset button is present, update the text.
    if (affectedControlsCount === 0 && !resetButton) {
      console.log('updateFilterCount', 'no filters and no reset button, skipping...');
      return;
    } else if (affectedControlsCount === 0 && resetButton) {
      console.log('updateFilterCount', 'no filters and reset button found, removing...');
      resetButton.remove();
      return;
    } else if (affectedControlsCount > 0 && !resetButton) {
      console.log('updateFilterCount', 'has filters and no reset button, creating...');
      resetButton = createResetFilterGroupButton(targetId, buttonText);
      selectedFilterArea.appendChild(resetButton);
      return;
    } else {
      console.log('updateFilterCount', 'has filters and reset button found, updating...');
      resetButton.querySelector('span').textContent = buttonText;
    }
  }

  /**
   * Reset form controls.
   * @param {NodeList} controls Form controls to reset
   */
  function resetFilterControls(controls) {
    controls.forEach(control => {
      if (control.type === 'checkbox' || control.type === 'radio') {
        // Uncheck checkboxes and radios
        control.checked = false;
      } else if (control.tagName === 'SELECT') {
        // Deselect all options
        Array.from(control.options).forEach(option => {
          option.selected = false;
        });

        // Let's find the exact Choices instance associated with this select
        // from window.choices by the control name (without [])
        const controlName = control.name.replace(/\[|\]/g, '')
        const choicesInstance = window.choices[controlName];
        if (!choicesInstance) {
          console.warn('Choices instance not found for select', control);
          return;
        }

        // Remove all selected items
        choicesInstance.removeActiveItems();
      }
    });
    
    // Dispatch change event to update any other UI elements that depend on form state
    const changeEvent = new Event('change', { bubbles: true });
    if (controls.length > 0) {
      Array.from(controls).forEach(control => control.dispatchEvent(changeEvent));
    }
  }

  /**
   * Count number of affected controls in a given NodeList.
   * @param {NodeList} controls 
   * @returns {Number} Number of affected controls
   */
  function countAffectedControls(controls) {
    console.log('countAffectedControls', controls);
    // Get all form controls in this accordion body, grouped by name
    const controlNames = new Set();
    controls.forEach(control => {
      if (control.name) {
        controlNames.add(control.name.replace('[]', ''));
      }
    });
    console.log('affected control names', controlNames);

    const form = controls[0].closest('form');
    console.log('found the form closest to given controls', controls, form);

    let affectedControlsCount = 0;
    
    // Count affected controls
    controlNames.forEach(name => {
      const namedControls = form.querySelectorAll(`[name="${name}"], [name="${name}[]"]`);
      console.log(`found controls with name "${name}`, namedControls);
      
      if (namedControls.length === 1 && namedControls[0].tagName === 'SELECT') {
        // For a single or multi-select, count selected options
        const selectedOptions = namedControls[0].selectedOptions;
        let selectedOptionsLength = 0;
        // Loop through selected options and count them, making sure to skip the default empty option.
        // Note, that selectedOptions is a NodeList, not an array, so we can't use forEach directly.
        Array.from(selectedOptions).forEach(option => {
          if (option.value) {
            selectedOptionsLength++;
          }
        });
        console.log('processing select control', namedControls[0], selectedOptions, selectedOptionsLength);
        if (selectedOptionsLength > 0) {
          affectedControlsCount += selectedOptionsLength;
        }
      } else {
        // For checkboxes or radio buttons
        console.log('processing checkbox or radio controls', namedControls);
        namedControls.forEach(control => {
          if ((control.type === 'checkbox' || control.type === 'radio') && control.checked) {
            affectedControlsCount++;
          }
        });
      }
    });

    return affectedControlsCount;
  }

})(document, window);
