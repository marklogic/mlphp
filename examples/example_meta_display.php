<!DOCTYPE html>
<html lang="en-us">
<head>
<title>Example Code: Manage Document Metadata</title>
<link type="text/css" href="styles.css" rel="stylesheet">
<link type="text/css" href="../external/prettify/prettify.css" rel="stylesheet">
<script type="text/javascript" src="../external/prettify/prettify.js"></script>
</head>

<body onload="prettyPrint()">

<div id="wrapper">

<div class="examples-subtitle"><a href="index.php">Example Code</a></div>
<h1>Manage Document Metadata</h1>

<div class="code-links"><strong>Display code</strong> | <a href="example_meta.php">Execute code</a></div>

<pre class="prettyprint lang-html">
// Create a REST client object using global variables
$client = new RESTClient($mlphp['host'], $mlphp['port'], $mlphp['path'], $mlphp['version'],
                         $mlphp['username'], $mlphp['password'], $mlphp['auth']);

// Write a sample document
$doc1 = new Document($client);
$file1 = 'example.xml';
$doc1->setContentFile($file1);
$doc1->setContentType('application/xml');
$uri1 = '/' . $file1;
$doc1->write($uri1);


<span class="code-section">// Reset metadata to default by deleting</span>
$doc1->deleteMetadata();				// Delete the metadata for the document
$meta1 = $doc1->readMetadata();			// Read the metadata for the document
echo "Starting collections:\n";
print_r($meta1->getCollections());		// Read and display the collections metadata for the document
echo "Starting properties:\n";
print_r($meta1->getProperties());		// Read and display the properties metadata for the document
echo "Starting permissions:\n";
print_r($meta1->getPermissions());		// Read and display the permissions metadata for the document
echo "Starting quality:\n";
print_r($meta1->getQuality());			// Read and display the quality metadata for the document
echo "\n\n\n";


<span class="code-section">// Collections</span>
$collections = array('food', 'fruit');		// Define an array of collections
$meta1->addCollections($collections);		// Add the collections to the metadata object
echo "Two collections added:\n";
print_r($meta1->getCollections());		// Read and display the collections in the metadata object
echo "One more collection added:\n";
$meta1->addCollections('red');			// Add a collection to the metadata object as a string
print_r($meta1->getCollections());		// Read and display the collections in the metadata object
echo "One collection deleted:\n";
$meta1->deleteCollections('fruit');		// Delete a collection from the metadata object
print_r($meta1->getCollections());		// Read and display the collections in the metadata object
$doc1->writeMetadata($meta1);			// Write the metadata object for the document to the database
echo "Final written collections:\n";
$meta1 = $doc1->readMetadata();			// Read the metadata for the document
print_r($meta1->getCollections());		// Read and display the collections in the metadata object
echo "\n\n\n";


<span class="code-section">// Properties</span>
$properties = array('size' => 'large', 		// Define an associative array of properties
                    'color' => 'blue',
                    'qty' => 12);
$meta1->addProperties($properties);		// Add the properties to the metadata object
echo "Three properties added:\n";
print_r($meta1->getProperties());		// Read and display the properties in the metadata object
$meta1->deleteProperties('color');		// Delete a property from the metadata object
echo "One property deleted:\n";
print_r($meta1->getProperties());		// Read and display the properties in the metadata object
$doc1->writeMetadata($meta1);			// Write the metadata object for the document to the database
echo "Final written properties:\n";
$meta1 = $doc1->readMetadata();			// Read the metadata for the document
print_r($meta1->getProperties());		// Read and display the properties in the metadata object
echo "\n\n\n";


<span class="code-section">// Permissions</span>
// PREREQUISITE: Create 'doc-reader', 'doc-admin', and 'doc-editor' roles in Admin interface
$perm1 = new Permission('doc-reader', 'read');	// Create a new Permission object for doc-reader
$perm2 = new Permission('doc-admin', 		// Create a new Permission object for doc-admin
                        array('read',
                              'update',
                              'insert')
                        );
$perm_arr = array($perm1, $perm2);		// Store the permissions objects in an array
$meta1->addPermissions($perm_arr);		// Add the permissions to the metadata object
echo "Permissions added for doc-reader and doc-admin:\n";
print_r($meta1->getPermissions());		// Read and display the permissions in the metadata object
$perm3 = new Permission('doc-editor', 		// Create a new Permission object for doc-editor
                        array('read',
                              'update')
                        );
$meta1->addPermissions($perm3);			// Add the permission to the metadata object
echo "Permission added for doc-editor:\n";
print_r($meta1->getPermissions());		// Read and display the permissions in the metadata object
$meta1->deletePermissions('doc-reader');		// Delete the permission object for doc-reader
echo "Permission deleted for doc-reader:\n";
print_r($meta1->getPermissions());		// Read and display the permissions in the metadata object
$doc1->writeMetadata($meta1);			// Write the metadata object for the document to the database
echo "Final written permissions:\n";
$meta1 = $doc1->readMetadata();			// Read the metadata for the document
print_r($meta1->getPermissions());		// Read and display the permissions in the metadata object
echo "\n\n\n";


<span class="code-section">// Quality</span>
$quality = 9;					// Define a quality value
$meta1->setQuality($quality);			// Set the quality for the metadata object
echo "Quality updated:\n";
echo $meta1->getQuality() . PHP_EOL;		// Read and display the quality of the metadata object
$doc1->writeMetadata($meta1);			// Write the metadata object for the document to the database
echo "Final written quality:\n";
$meta1 = $doc1->readMetadata();			// Read the metadata for the document
print_r($meta1->getQuality());			// Read and display the quality for the metadata object
echo "\n\n\n";


<span class="code-section">// Update multiple metadata at once via method chaining</span>
$meta1->addCollections(array('sugary', 'fresh'))->addProperties(array('created' => '2012-11-10', 'rating' => '9/10'));
$perm4 = new Permission('doc-editor', array('read', 'update', 'insert'));
$meta1->addPermissions($perm4)->setQuality($meta1->getQuality() + 1);
echo "Metadata (collections, properties, permissions, and quality) updated via method chaining:\n";
// Write, read, and display
$doc1->writeMetadata($meta1);
print_r($doc1->readMetadata());
echo "\n\n\n";


<span class="code-section">// Update metadata simultaneously with document write</span>
$doc2 = new Document($client);
$doc2->setContent('More content');
$uri2 = '/example_updated.xml';
// Add metadata as params
$params2 = array('collection' => 'round',
                 'prop:status' => 'current',
                 'perm:doc-editor' => 'insert',
                 'quality' => 99
                );
echo "Metadata updated via params:\n";
// Write, read, and display
$doc2->write($uri2, $params2);
print_r($doc2->readMetadata());
echo "\n\n\n";
</pre>

</div>

</body>
</html>