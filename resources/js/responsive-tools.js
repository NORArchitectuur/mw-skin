/**
 * This script handles responsive behavior for the tools menu items.
 * It creates copies of hidden items (#ca-edit, #ca-formedit, #ca-watch) 
 * in the content menu dropdown so they remain accessible on smaller viewports.
 */
$(document).ready(function() {
	// Items to be copied to the content menu on smaller screens
	const itemsToHandle = ['ca-edit', 'ca-formedit', 'ca-watch'];
	// Track created copies to easily remove them later
	let createdCopies = [];

	/**
	 * Check if the specified items are hidden by CSS
	 * @return {boolean} True if items are hidden
	 */
	function areItemsHidden() {
		const firstItem = $('#' + itemsToHandle[0]);
		if (firstItem.length === 0) {
			return false;
		}
		
		const display = firstItem.css('display');
		return display === 'none';
	}

	/**
	 * Create copies in content menu
	 */
	function createCopiesInContentMenu() {
		// First remove any existing copies to avoid duplicates
		removeCopiesFromContentMenu();
		
		const contentMenu = $('#contentMenu');
		if (contentMenu.length === 0) {
			return;
		}
		
		// Find the "Meer" label or equivalent
		let insertPoint = null;
		const infoLabels = contentMenu.find('.navigation-item.info');
		
		if (infoLabels.length >= 2) {
			// Use the second info label (typically "Meer")
			insertPoint = infoLabels.eq(1);
		} else if (infoLabels.length === 1) {
			// Use the first info label as fallback
			insertPoint = infoLabels.eq(0);
		} else {
			// Just use the first item in the menu
			insertPoint = contentMenu.children().first();
		}
		
		if (insertPoint.length === 0) {
			return;
		}

		$.each(itemsToHandle, function(index, id) {
			const originalItem = $('#' + id);
			if (originalItem.length === 0) {
				return;
			}
			
			// Create a clean copy of the item
			const copy = $('<li></li>')
				.attr('id', id + '-copy')
				.attr('class', 'mw-list-item navigation-item');
			
			// Store reference to the created copy
			createdCopies.push(copy);
			
			const originalLink = originalItem.find('a');
			const href = originalLink.attr('href');
			const title = originalLink.attr('title');
			const accesskey = originalLink.attr('accesskey');
			const linkText = originalLink.text().trim();
			
			// Create a new clean link
			const newLink = $('<a></a>')
				.attr('href', href)
				.attr('class', 'navigation-link')
				.text(linkText);
			
			if (title) {
				newLink.attr('title', title);
			}
			
			if (accesskey) {
				newLink.attr('accesskey', accesskey);
			}
			
			// Add the link to our copy
			copy.append(newLink);
			
			// Insert the copy into the content menu
			copy.insertAfter(insertPoint);
			
			// Update insert point for next item
			insertPoint = copy;
		});
	}

	/**
	 * Remove copies from content menu
	 */
	function removeCopiesFromContentMenu() {
		// Remove all copies we've created
		$.each(createdCopies, function(index, copy) {
			copy.remove();
		});
		
		// Reset our tracking array
		createdCopies = [];
	}

	/**
	 * Handle responsive behavior
	 */
	function handleResponsiveTools() {
		if (areItemsHidden()) {
			createCopiesInContentMenu();
		} else {
			removeCopiesFromContentMenu();
		}
	}

	// Initialize responsive tools after a short delay to ensure elements are fully loaded
	setTimeout(function() {
		handleResponsiveTools();
		
		// Handle resize events with debounce
		let resizeTimer;
		$(window).on('resize', function() {
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(handleResponsiveTools, 250);
		});
	}, 500);
}); 