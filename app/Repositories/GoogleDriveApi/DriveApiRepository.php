<?php

namespace App\Repositories\GoogleDriveApi;

use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class DriveApiRepository implements DriveApiInterface
{
    /**
     * Get all the contents of a specified directory
     * 
     * @param string $directoryPath
     * 
     * @return Illuminate\Support\Collection
     */
    public function getDirectoryContents($directoryPath): Collection
    {
        return $this->getContentsOfADirectory($directoryPath, false);
    }

    /**
     * Find directory in google drive by its name in a specified parent directory
     * 
     * @param string $directoryName
     * @param string $parentDirectoryPath
     * @param string $isRecursive
     * 
     * @return array|null
     */
    public function findDirectoryByName($directoryName, $parentDirectoryPath = '/', $isRecursive = false): ?array
    {
        // dump('called');
        $contents = $this->getContentsOfADirectory($parentDirectoryPath, $isRecursive);

        return $contents->where('type', '=', 'dir')
            ->where('name', '=', $directoryName)
            ->first();
    }

    /**
     * Find all the directories in google drive by its name in a specified parent directory
     * 
     * @param string $directoryName
     * @param string $parentDirectoryPath
     * @param string $isRecursive
     * 
     * @return array|null
     */
    public function findAllDirectoryByName($directoryName, $parentDirectoryPath = '/', $isRecursive = false): ?array
    {
        $contents = $this->getContentsOfADirectory($parentDirectoryPath, $isRecursive);

        return array_values($contents->where('type', '=', 'dir')
            ->where('name', '=', $directoryName)
            ->all());
    }

    /**
     * Find file in google drive by its name in a specified parent directory
     * 
     * @param string $fileName
     * @param string $parentDirectoryPath
     * @param string $isRecursive
     * 
     * @return array|null
     */
    public function findFileByName($fileName, $parentDirectoryPath = '/', $isRecursive = false): ?array
    {
        $contents = $this->getContentsOfADirectory($parentDirectoryPath, $isRecursive);

        return $contents->where('type', '=', 'file')
            ->where('filename', '=', pathinfo($fileName, PATHINFO_FILENAME))
            ->where('extension', '=', pathinfo($fileName, PATHINFO_EXTENSION))
            ->first();
    }

    /**
     * Create a directory in the specified path
     * 
     * @param string $path
     * 
     * @return bool
     */
    public function createDirectory($path): bool
    {
        return Storage::cloud()->makeDirectory($path);
    }

    /**
     * Delete directory by directory name in a specified parent directory
     * 
     * @param string $directoryName
     * @param string $parentDirectory
     * 
     * @return bool
     */
    public function deleteDirectoryByName($directoryName, $parentDirectory): bool
    {
        $directories = $this->findAllDirectoryByName($directoryName, $parentDirectory);

        if (count($directories) == 0) {
            return false;
        }

        foreach ($directories as $directory) {
            Storage::cloud()->deleteDirectory($directory['path']);
        }

        return true;
    }

    /**
     * Get the raw data of a file of a specified file name
     * 
     * @param array $file 
     * 
     * @return Illuminate\Http\Response
     */
    public function getRawData($file): Response
    {
        $rawData = Storage::cloud()->get($file['path']);

        return response($rawData, 200)
            ->header('ContentType', $file['mimetype'])
            ->header('Content-Disposition', "attachment; filename='" . $file['name'] . "'");
    }

    /**
     * Get the file by name inside a parent -> child directory
     * 
     * @param string $fileName
     * @param string $parentDirectoryName
     * @param string $childDirectoryName
     * 
     * @return array 
     */
    public function getFile($fileName, $parentDirectoryName, $childDirectoryName): array
    {
        // find the parent directory 
        $dir = $this->findDirectoryByName($parentDirectoryName);

        // find the child directory inside the parent directory 
        $childDir = $this->findDirectoryByName($childDirectoryName, $dir['path']);

        // find the file inside the child directory
        return $this->findFileByName($fileName, $childDir['path']);
    }

    /**
     * Get all the contents of a directory
     * 
     * @param string $parentDirectoryPath
     * @param bool $isRecursive
     * 
     * @return Illuminate\Support\Collection
     */
    private function getContentsOfADirectory($parentDirectoryPath, $isRecursive): Collection
    {
        return collect(Storage::cloud()->listContents($parentDirectoryPath, $isRecursive));
    }
}
