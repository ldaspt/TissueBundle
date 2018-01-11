# Usage

## Scanning a file upload

Let's assume you allow your users to upload files to your website. Odds are you want to be sure those files do not contain
any viruses so you want to scan those files before permanently storing them somewhere on your server.

First we need a form that let's us process a file upload. If you created a separate `FormType` for it (good boy!),
it would look something like this:

```php
<?php

namespace CL\Bundle\TissueDemoBundle\Form\Type;

use CL\Bundle\TissueBundle\Validator\Constraints\CleanFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class DemoUploadType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('uploaded_file', 'file', [
            'constraints' => [
                new CleanFile(true) // the default option is 'autoRemove', here we set it to true
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'demo_upload';
    }
}
```

NOTE: I kept this form simple and added constraints directly, your application might have the constraints loaded from
annotations in a related `data_class`, or you might not even use a form,  but it all works the same way.


### So what does the constraint do?

During validation, the constraint will cause the adapter you have configured to scan the uploaded file before passing it
on to the inherited (`File`) constraint.
If a virus is detected, further validation is stopped and an error is raised accordingly. If you set the option to do so,
the uploaded file is also removed immediately.


### Example of an upload in a controller

Below is an example of what your action could look like if you used the form above to upload files:
```php
<?php

namespace CL\Bundle\TissueDemoBundle\Controller;

use CL\Bundle\TissueDemoBundle\Form\Type\DemoUploadType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class DemoController extends Controller
{
    public function uploadAction(Request $request)
    {
        $form = $this->createForm(new DemoUploadType());
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form->getData()['uploaded_file'];
            if ($form->isValid()) {
                // if we reached this point the file seems to be clean (according to your scanner)!
                // so maybe move the upload to some permanent storage?
                //
                // $permanentDir = '/path/to/permanent/storage';
                // $uploadedFile->move($permanentDir, uniqid());
                // $newLocation = $uploadedFile->getRealPath();
            } else {
                // hm something funny went on, the scanner seems to think the file is infected...
                // you'd be wise to remove this file now...

                // NOTE: we don't have to do this here because we configured the constraint
                // with the `autoRemove`-option set to TRUE
                //unlink($uploadedFile->getRealpath());
            }
        }

        // render the form, along with any validation errors that may have been raised
        return $this->render('CLTissueDemoBundle:Demo:upload.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function successAction()
    {
        return $this->render('CLTissueDemoBundle:Demo:success.html.twig');
    }
}
```


## Don't forget...

Read [the warning about security issues](https://github.com/cleentfaar/CLTissueBundle#warning)
