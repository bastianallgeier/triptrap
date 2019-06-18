import { Node, Plugin } from 'tiptap'
import { nodeInputRule } from 'tiptap-commands'
import { setBlockType, textblockTypeInputRule, toggleBlockType } from 'tiptap-commands'

/**
 * Matches following attributes in Markdown-typed image: [, alt, src, title]
 *
 * Example:
 * ![Lorem](image.jpg) -> [, "Lorem", "image.jpg"]
 * ![](image.jpg "Ipsum") -> [, "", "image.jpg", "Ipsum"]
 * ![Lorem](image.jpg "Ipsum") -> [, "Lorem", "image.jpg", "Ipsum"]
 */
const IMAGE_INPUT_REGEX = /!\[(.+|:?)\]\((\S+)(?:(?:\s+)["'](\S+)["'])?\)/

export default class Image extends Node {

  get name() {
    return 'image'
  }

  get schema() {
    return {
      defining: true,
      attrs: {
        src: {},
        filename: {
          default: null
        },
        alt: {
          default: null,
        },
        title: {
          default: null,
        },
      },
      group: 'block',
      selectable: true,
      draggable: true,
      parseDOM: [
        {
          tag: 'img[src]',
          getAttrs: dom => ({
            src: dom.getAttribute('src'),
            title: dom.getAttribute('title'),
            alt: dom.getAttribute('alt'),
          }),
        },
      ],
      toDOM: node => ['img', node.attrs],
    }
  }

  commands({ type, schema }) {
    return attrs => (state, dispatch) => {
      const { selection } = state
      const position = selection.$cursor ? selection.$cursor.pos : selection.$to.pos
      const node = type.create(attrs)
      const transaction = state.tr.insert(position, node)
      dispatch(transaction)
    }
  }

  inputRules({ type }) {
    return [
      nodeInputRule(IMAGE_INPUT_REGEX, type, match => {
        const [, alt, src, title] = match
        return {
          src,
          alt,
          title,
        }
      }),
    ]
  }

  get view() {
    return {
      props: ['node', 'updateAttrs', 'view', 'selected'],
      computed: {
        src: {
          get() {
            return this.node.attrs.src
          },
          set(src) {
            this.updateAttrs({
              src,
            })
          },
        },
      },
      render(h) {
        return h("figure", {
          class: {image: true, 'ProseMirror-selectednode': this.selected},
        }, [
          h("img", {
            attrs: { src: this.src },
          }),
          h("figcaption", this.node.attrs.filename)
        ]);
      }
    }
  }

}
