<?php

namespace App\Repositories\GoogleDriveApi;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;

interface DriveApiInterface
{
    /**
     * Find directory in google drive by its name in a specified parent directory
     * 
     * @param string $directoryName
     * @param string $parentDirectoryPath
     * @param string $isRecursive
     * 
     * @return array/null
     */
    public function findDirectoryByName($directoryName, $parentDirectoryPath = '/', $isRecursive = false): ?array;

    /**
     * Find all the directories in google drive by its name in a specified parent directory
     * 
     * @param string $directoryName
     * @param string $parentDirectoryPath
     * @param string $isRecursive
     * 
     * @return array|null
     */
    public function findAllDirectoryByName($directoryName, $parentDirectoryPath = '/', $isRecursive = false): ?array;

    /**
     * Find file in google drive by its name in a specified parent directory
     * 
     * @param string $fileName
     * @param string $parentDirectoryPath
     * @param string $isRecursive
     * 
     * @return array|null
     */
    public function findFileByName($fileName, $parentDirectoryPath = '/', $isRecursive = false): ?array;

    /**
     * Create a directory in the specified path
     * 
     * @param string $path
     * 
     * @return bool
     */
    public function createDirectory($path): bool;

    /**
     * Delete directory by directory name in a specified parent directory
     * 
     * @param string $directoryName
     * @param string $parentDirectory
     * 
     * @return bool
     */
    public function deleteDirectoryByName($directoryName, $parentDirectory): bool;

    /**
     * Get the raw data of a file of a specified file name
     * 
     * @param array $file 
     * 
     * @return Illuminate\Http\Response
     */
    public function getRawData($file): Response;

    /**
     * Get the file by name inside a parent -> child directory
     * 
     * @param string $fileName
     * @param string $parentDirectoryName
     * @param string $childDirectoryName
     * 
     * @return array
     */
    public function getFile($fileName, $parentDirectoryName, $childDirectoryName): array;

    /**
     * Get all the contents of a specified directory
     * 
     * @param string $directoryPath
     * 
     * @return Illuminate\Support\Collection
     */
    public function getDirectoryContents($directoryPath): Collection;
}
