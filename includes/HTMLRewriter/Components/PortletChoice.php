<?php

namespace MediaWiki\Skin\NORA\HTMLRewriter\Components;

use DOMDocument;
use DOMElement;
use DOMXPath;
use MediaWiki\Skin\NORA\HTMLRewriter\RewriteComponent;

/**
 * A portlet rewriter that transforms namespace tabs into radio button style choices.
 */
class PortletChoice extends RewriteComponent {

	/**
	 * Rewrites the given namespace tabs into radio button style options.
	 *
	 * @param string $containerClass Class applied to the container div
	 * @param string $radioClass Class applied to each radio button
	 * @param string $labelClass Class applied to each label
	 * @param array $removeIds An array containing the IDs of the items to be removed
	 *
	 * @return array An array containing the modified HTML string
	 */
	public function rewrite(
		string $containerClass = 'portlet-choice-container',
		string $radioClass = 'portlet-choice-radio',
		string $labelClass = 'portlet-choice-label',
		array $removeIds = []
	): array {
		$portlet = $this->getComponentData();
		
		if (!isset($portlet['html-items'])) {
			return $portlet;
		}

		// Remove items by ID if needed
		if (count($removeIds) > 0) {
			$portlet['html-items'] = $this->removeItemsById($portlet['html-items'], $removeIds);
		}

		// Parse the original HTML
		$dom = $this->getDOMDocument($portlet['html-items']);
		$xpath = new DOMXPath($dom);
		
		// Get all list items
		$items = $xpath->query('//li');
		
		// Create new container
		$container = $dom->createElement('div');
		$container->setAttribute('class', $containerClass);
		
		$firstItem = true;
		
		// Process each list item and convert to radio button
		foreach ($items as $item) {
			/** @var DOMElement $item */
			$id = $item->getAttribute('id');
			$isSelected = $item->hasAttribute('class') && strpos($item->getAttribute('class'), 'selected') !== false;
			
			// Get the link element within the list item
			$linkNode = $xpath->query('./a', $item)->item(0);
			if (!$linkNode) {
				continue;
			}
			
			/** @var DOMElement $link */
			$link = $linkNode;
			$href = $link->getAttribute('href');
			$title = $link->getAttribute('title');
			$text = $link->textContent;
			$radioId = 'radio-' . $id;
			
			// Create radio input
			$radio = $dom->createElement('input');
			$radio->setAttribute('type', 'radio');
			$radio->setAttribute('id', $radioId);
			$radio->setAttribute('name', 'namespace-choice');
			$radio->setAttribute('class', $radioClass);
			$radio->setAttribute('value', $id);
			$radio->setAttribute('data-href', $href);
			
			if ($isSelected || ($firstItem && !$isSelected)) {
				$radio->setAttribute('checked', 'checked');
				$firstItem = false;
			}
			
			// Create label
			$label = $dom->createElement('label');
			$label->setAttribute('for', $radioId);
			$label->setAttribute('class', $labelClass);
			$label->setAttribute('title', $title);
			$label->textContent = $text;
			
			// Add to container
			$container->appendChild($radio);
			$container->appendChild($label);
		}
		
		// Replace the original HTML
		$portlet['html-items'] = str_replace('<?xml encoding="utf-8" ?>', '', $dom->saveHTML($container));
		
		return $portlet;
	}
} 